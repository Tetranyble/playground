import Image from "next/image";
import Link from "next/link";

const Home = () =>{
  return (
    <main className="container-fluid center-column">
      <section className="logo-container">
        <div>Logo Here</div>
        <Link href="/dashboard">DEVELOPER'S ASSESSMENT</Link>
      </section>
    </main>
  );
}

export default Home;
