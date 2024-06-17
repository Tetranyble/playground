'use client'
import EditorJS from '@editorjs/editorjs'
import { FC, useRef, useEffect, useState } from 'react';
import api from '../lib/axios'
type EditorProps = {
 
}
export const Editor:FC<EditorProps> = ({}: EditorProps) => {
    const ref = useRef<EditorJS>();
    const [isMounted, setIsMounted] = useState(false);

    const initializeEditor = async () => {
        const EditorJS = (await import("@editorjs/editorjs")).default;
        const Header = (await import("@editorjs/header")).default;
        const Image = (await import("@editorjs/image")).default;
        const List = (await import("@editorjs/list")).default;
        const LinkTool = (await import("@editorjs/link")).default;
        const YoutubeEmbed = (await import("editorjs-youtube-embed")).default;
        const Embed = (await import('@editorjs/embed')).default;
        const Marker = (await import('@editorjs/marker')).default;
        const Quote = (await import('@editorjs/quote')).default;
        if(!ref.current){
            const editor = new EditorJS({
                holder: "editorjs",
                placeholder: "Add content",
                tools: {
                    header: {
                      config: {
                        placeholder: 'Enter content',
                        levels: [2,3,4],
                        defaultLevel: 3,
                      }
                    },
                    // paragraph: {
                    //     class: Paragraph,
                    //     inlineToolbar: true,
                    //   },
                      quote: {
                        class: Quote,
                        inlineToolbar: true,
                        shortcut: 'CMD+SHIFT+O',
                        config: {
                          quotePlaceholder: 'Enter a quote',
                          captionPlaceholder: 'Quote\'s author',
                        },
                      },
                    linkTool:LinkTool,
                    image: {
                        class: Image,
                        config: {
                          uploader: {
                            uploadByFile(file: File) {
                              // upload to uploadthing
                              return api.post('media', {
                                media: file
                              },{
                                headers: {
                                  'Content-Type': 'multipart/form-data'
                                }
                              })
                              .then((response)=>{

                                  console.log(response.data.data);
                                  return {
                                    success: 1,
                                    file: {
                                      url: response.data.data.path,
                                    },
                                  }
                              }).catch(error =>{
                                console.log(error)
                              })
                            },
                          },
                        },
                      },
                    list: List,
                    embed: {
                        class: Embed,
                        inlineToolbar: true,
                        config: {
                          services: {
                            youtube: true,
                            coub: true,
                            facebook: true
                          }
                        }
                      },
                    youtubeEmbed: YoutubeEmbed,
                    Marker: {
                        class: Marker,
                        shortcut: 'CMD+SHIFT+M',
                      }
                }
                
            })
            ref.current = editor;
        }
    }
    useEffect(()=>{
        if(typeof window !== "undefined"){
            setIsMounted(true);
        }
    },[])
    useEffect(()=>{
        const init = async ()=>{
            await initializeEditor();
        };
        if(isMounted){
            init()
        }
        return ()=>{
            if(ref.current){
                ref.current.destroy();
            }
        }
    },[isMounted])

    const save = () => {
        if(ref.current){
            ref.current.save().then(output =>{
                console.log("article data ", output)
                alert(JSON.stringify(output));
            })
        }
    }
    return ( 
        <div className="editor-container">
            <form>
                <div className="editor">
                    <div className="editor-header"></div>
                    <div className="input-container">
                        <input className="input" name="name" placeholder="Add post title"/>
                    </div>
                    <div id="editorjs"></div>
                </div> 
                <div className="editor-footer">
                    <button className="btn" onClick={save}>Post</button>
                </div> 
            </form>
        </div>   
    );
}