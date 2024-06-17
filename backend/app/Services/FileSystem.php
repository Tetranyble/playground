<?php

namespace App\Services;

use App\Enums\Disk;
use App\Traits\FileSystemTrait;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileSystem
{
    use FileSystemTrait;

    protected $storage;

    public Disk $disk;

    public function __construct(Disk $storage = Disk::PUBLIC)
    {
        $this->storage = Storage::getFacadeRoot();
        $this->storage->disk($storage->value);
        $this->disk = Disk::fromValue(config('filesystems.default')) ?? $storage;
    }

    /**
     * @return mixed
     */
    public function store(string|UploadedFile|File $file, string $location = 'images', Disk $disk = Disk::PRIVATE)
    {
        return $this->storeAs(
            $file,
            $this->rename($file),
            $location,
            $disk
        );
    }

    /**
     * @return mixed
     */
    public function storeAs(string|UploadedFile $file, string $name, string $location = 'images', Disk $disk = Disk::PRIVATE)
    {
        return $this->storage->disk($disk->value)
            ->putFileAs(
                $location,
                ($file instanceof UploadedFile) ? $file : new File($file),
                $name
            );
    }

    /**
     * @return mixed
     */
    public function patch(string $location, string $oldFilePath, Disk $provider = Disk::PRIVATE)
    {
        return $this->storage
            ->disk($provider->value)
            ->move($oldFilePath, $location);
    }

    /**
     * @return mixed
     */
    public function show(string $file, Disk $disk = Disk::PRIVATE, int $time = 60)
    {
        return ($disk === Disk::S3PRIVATE) ?
            $this->signUrl($file, $disk, $time) : (
                ($disk === Disk::PRIVATE || $disk === Disk::PUBLIC) ?
                    $this->showLocal($file, $disk) : (
                        ($disk === Disk::YOUTUBE || $disk === Disk::VIMEO) ?
                            $file :
                            $this->storage
                                ->disk($disk->value)
                                ->url($file)
                    )
            );

    }

    /**
     * @return mixed
     */
    public function showLocal(string $file, Disk $provider = Disk::PRIVATE)
    {
        return $this->storage
            ->disk($provider->value)
            ->url($file);
    }

    public function download(string $file, Disk $disk, array $options = []): StreamedResponse
    {
        return Storage::disk($disk->value)
            ->download($file);
    }

    public function disk(Disk $storage)
    {
        $this->storage->disk($storage->value);

        return $this;
    }

    public function rename(string|UploadedFile|null $file, string $extension = '.png')
    {
        $name = Str::uuid()->toString().'-'.now()->format('Y-m-d-H-i-s');

        return ($file instanceof UploadedFile) ? $name.'.'.$file->extension() :
            $name.$extension;
    }

    /**
     * @param  resource  $resource
     */
    public function writeStream($resource, Disk $disk, string $path = 'resources', array $options = []): bool
    {
        return $this->storage
            ->disk($disk->value)
            ->writeStream(
                $path,
                $resource,
                $options
            );
    }

    /**
     * @return resource|null
     */
    public function readStream(string $file, Disk $disk)
    {
        return $this->storage->disk($disk->value)
            ->getDriver()
            ->readStream($file);
    }

    public function makeFolder(string $path, Disk $disk): bool
    {

        return $this->storage
            ->disk($disk->value)
            ->makeDirectory($path);
    }

    public function deleteFolder(string $path, Disk $disk): bool
    {
        return $this->storage
            ->disk($disk->value)
            ->deleteDirectory($path);
    }

    public function fileContent(string $file, Disk $disk): ?string
    {
        return $this->storage
            ->disk($disk->value)
            ->deleteDirectory($file);
    }

    /**
     * @return mixed
     */
    public function put(File|UploadedFile|StreamInterface|string $data, string $path = 'media', Disk $disk = Disk::PUBLIC)
    {
        return $this->storage
            ->disk($disk->value)
            ->put(
                $path,
                $data
            );
    }

    public function destroy(string $file, Disk $disk): bool
    {
        return $this->storage
            ->disk($disk->value)
            ->delete($file);
    }

    /**
     * @return string|bool
     *
     * @throws FtpClientException
     */
    public function findAndStream(
        FTPServiceClient $client,
        string $file,
        Disk $from = Disk::FTP,
        Disk $to = Disk::S3PUBLIC,
    ): string|bool|array {

        $filename = $this->filename($file);
        $path = $client->find($filename, 'uploads') ?:
            $client->find($filename, 'images');

        if (! empty($path)) {
            $newFile = "products/images/{$this->filename($path[0]['path'])}";
            $stream = $this->readStream(
                $path[0]['path'],
                $from
            );
            $this->put(
                $newFile,
                $stream,
                $to
            );

            return $newFile;
        }

        return false;
    }

    public function resizeAndStore(
        File|UploadedFile|StreamInterface|string $filename,
        int $width,
        int $height,
        string $location = 'app/public',
        Disk $disk = Disk::PRIVATE
    ): string {

        $stream = (new ImageManipulation())
            ->resize(
                $filename,
                $width,
                $height
            )->encode('png')
            ->stream();
        $file = 'photos/'.$this->rename('');

        $this->put(
            $location.'/'.$file,
            $stream,
            $disk
        );

        return $file;
    }

    /**
     * @return string
     */
    public function mime(string $file, Disk $disk = Disk::S3PUBLIC, bool $driver = false): string|false
    {
        return $driver ? (new \Illuminate\Filesystem\Filesystem())->mimeType($file) :
            $this->storage
                ->disk($disk->value)
                ->mimeType($file);
    }

    /**
     * @return string
     */
    public function size(string $file, Disk $disk = Disk::S3PUBLIC, bool $driver = false): int
    {
        return $driver ? (new \Illuminate\Filesystem\Filesystem())->size($file) :
            $this->storage
                ->disk($disk->value)
                ->size($file);

    }

    public function signUrl(string $path, Disk $disk = Disk::S3PRIVATE, int $time = 60)
    {
        return $this->storage->disk($disk->value)->temporaryUrl(
            $path,
            now()->addMinutes($time)
        );
    }

    public function pull(string $url): string
    {
        return file_get_contents($url);
    }

    public function push(string $url, string $path, Disk $disk = Disk::PUBLIC)
    {
        return $this->put(
            $this->pull($url),
            $path,
            $disk
        );
    }
}
