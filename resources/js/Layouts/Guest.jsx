import React,{useEffect, useState, useRef} from 'react'
import { Link, Head } from '@inertiajs/inertia-react'
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"
import {faRocket} from "@fortawesome/free-solid-svg-icons"
import {toast, ToastContainer} from "react-toastify"

export default function Guest(props) {

    const isMounted = useRef(false)
    const [showNavbar, setShowNavbar] = useState(true)
    const [email, setEmail] = useState("")

    function toggleNavbar(){
        if (showNavbar) {
            setShowNavbar(false)
        } else {
            setShowNavbar(true)
        }
    }

    function setNavbarVisibility(){
        if (document.documentElement.clientWidth > 1000) {
            setShowNavbar(true)
        } else {
            setShowNavbar(false)
        }
    }

    function onEmailChange(event){
        setEmail(event.target.value)
    }

    function onSubsciptionFormSubmit(e){
        e.preventDefault()
        setEmail("")
        toast.success("Email subscribed")
    }

    useEffect(()=>{
        if(!isMounted.current){
            if (document.documentElement.clientWidth <= 1000) {
                setShowNavbar(false)
            }
        }

        isMounted.current = true

        window.addEventListener('resize', setNavbarVisibility)

        return () => {
            window.removeEventListener('resize', setNavbarVisibility)
        }
    })

    return (
        <>
            <Head>
                <title>{props.title ? props.title : null}</title>
            </Head>
            <header className="flex items-center justify-between flex-wrap bg-indigo-800 p-7 fixed w-full z-50">
                <ToastContainer/>
                <Link href="/" className="flex items-center flex-shrink-0 text-white mr-6">
                    <FontAwesomeIcon icon={faRocket} className="text-2xl mr-3"></FontAwesomeIcon>
                    <span className="font-semibold text-xl tracking-tight">Cosmos Oddysey</span>
                </Link>
                <div className="block lg:hidden">
                    <button
                        onClick={toggleNavbar}
                        className="flex items-center px-3 py-2 border rounded text-white border-indigo-500 hover:text-white hover:border-white"
                        id="navbar-btn">
                        <svg className="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <title>Menu</title>
                            <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                        </svg>
                    </button>
                </div>
                <div className="w-full block flex-grow lg:flex lg:items-center lg:w-auto" id="navbar" style={{display: showNavbar ? 'block' : 'none' }}>
                    <div className="text-sm lg:flex-grow  text-center lg:text-right">
                        <Link className="block mt-4 lg:inline-block lg:mt-0 text-white hover:text-teal-100 mr-8 text-lg" href="/">
                            Home
                        </Link>
                        <Link className="block mt-4 lg:inline-block lg:mt-0 text-white hover:text-teal-100 mr-8 text-lg" href="/booking">
                            Booking
                        </Link>
                    </div>
                </div>
            </header>
            <main className="flex-1 pt-20 pb-4 m-auto w-full">
                {props.children}
            </main>
            <footer className="flex flex-wrap justify-center bg-indigo-800 p-6">
                <div className="flex flex-wrap mb-4 w-full">
                    <div className="w-full sm:w-1/2 md:w-1/2 lg:w-1/4 ">
                        <h3 className="text-3xl py-4 text-white">About Us</h3>
                        <p className="text-white text-sm">We are a travel agency that believes in fair prices and giving
                            our customers the best space travelling experience possible. All our partners go through
                            passenger safety checks, technical inspections and quality checks in order to make sure your
                            travels are held to the highest standards.
                        </p>
                    </div>
                    <div className="w-full sm:w-1/2 md:w-1/2 lg:w-1/4 md:pl-8">
                        <h3 className="text-3xl py-4 text-white">Main</h3>
                        <ul>
                            <li><a href="#" className="text-white">Home</a></li>
                            <li><a href="#" className="text-white">Booking</a></li>
                        </ul>
                    </div>
                    <div className="w-full sm:w-1/2 md:w-1/2 lg:w-1/4">
                        <h3 className="text-3xl py-4 text-white">Subscribe</h3>
                        <form action="#" onSubmit={onSubsciptionFormSubmit}>
                            <div className="mb-4">
                                <input
                                    className="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                                    id="inline-full-name" type="text" placeholder="Email" required
                                    value={email}
                                    onChange={onEmailChange}
                                />
                            </div>
                            <button className="bg-indigo-900 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded"
                                    type="submit">
                                Submit
                            </button>
                        </form>
                    </div>
                </div>
            </footer>
        </>
    )
}
