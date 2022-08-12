import React, {useRef, useState} from 'react'
import { Link } from '@inertiajs/inertia-react'
import Guest from "@/Layouts/Guest"
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {
    faFacebookSquare,
    faInstagram,
    faLinkedin,
    faPinterestSquare,
    faTwitter
} from '@fortawesome/free-brands-svg-icons'
import { faHandPointer, faSort, faUser } from '@fortawesome/free-solid-svg-icons'
import {toast} from "react-toastify"

export default function Welcome(props) {

    const aboutUs = useRef(null)
    const [name, setName] = useState("")
    const [email, setEmail] = useState("")
    const [number, setNumber] = useState("")
    const [message, setMessage] = useState("")

    /**
     * Callback of name change
     * @param   {object} event change event
     * @return  {void}
     */
    function onNameChange(event){
        setName(event.target.value)
    }

    /**
     * Callback of email change
     * @param   {object} event change event
     * @return  {void}
     */
    function onEmailChange(event){
        setEmail(event.target.value)
    }

    /**
     * Callback of number change
     * @param   {object} event change event
     * @return  {void}
     */
    function onNumberChange(event){
        setNumber(event.target.value)
    }

    /**
     * Callback of message change
     * @param   {object} event change event
     * @return  {void}
     */
    function onMessageChange(event){
        setMessage(event.target.value)
    }

    /**
     * Scroll smoothly to "About us" section
     * @return  {void}
     */
    function scrollToAboutUs(){
        setTimeout(() => {
            let offsetY = aboutUs.current.offsetTop
            window.scrollTo({
                top: offsetY - 80,
                left: 0,
                behavior: "smooth"
            })
        }, 1)
    }

    /**
     * Callback to submit contact form
     * @param   {object} e submit event
     * @return  {void}
     */
    function submitContactForm(e) {
        e.preventDefault()
        setName("")
        setEmail("")
        setNumber("")
        setMessage("")
        toast.success("We'll contact you as soon as possible.")
    }

    return (
        <Guest>
            <div className="relative bg-white overflow-hidden">
                <div className="max-w-7xl">
                    <div
                        className="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32 pt-4">
                        <svg
                            className="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2"
                            fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                            <polygon points="50,0 100,0 50,100 0,100"/>
                        </svg>
                        <div className="relative pt-6 px-4 sm:px-6 lg:px-8">
                            <nav className="relative flex items-center justify-between sm:h-10 lg:justify-start"
                                 aria-label="Global">
                                <div className="flex items-center flex-grow flex-shrink-0 lg:flex-grow-0">
                                </div>
                            </nav>
                        </div>
                        <div className="mx-auto max-w-7xl px-8">
                            <div className="sm:text-center lg:text-left">
                                <div className="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                    <div className="block">Solar system</div>
                                    <div className="hidden xl:block"/>
                                    <div className="block text-indigo-800 sm:mt-8 mt-4">Space flights</div>
                                </div>
                                <p className="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                    Flights to your favourite planets for reasonable prices at your fingertips.
                                </p>
                                <div className="mt-5 sm:mt-10 sm:flex sm:justify-center lg:justify-start">
                                    <div className="rounded-md shadow">
                                        <Link href="/booking"
                                           className="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                            Book now
                                        </Link>
                                    </div>
                                    <div className="mt-3 sm:mt-0 sm:ml-3">
                                        <a href="#about-us"
                                           onClick={scrollToAboutUs}
                                           className="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10">
                                            About us
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="lg:absolute lg:inset-y-0 lg:right-0 lg:w-2/3">
                    <img
                         src="/images/solar-system.png"
                         alt=""/>
                </div>
            </div>
            <div className="" ref={aboutUs}>
                <div className="text-center">
                    <h1 className="text-5xl pt-10 pb-5 text-black">About Us</h1>
                </div>
                <div className="container mx-auto flex flex-wrap mb-8 justify-center">
                    <div className="px-6 py-4">
                        <p className="text-black text-xl text-center text-black">
                            We are a travel agency that believes in fair prices and giving our customers the best
                            space travelling experience possible. All our partners go through passenger safety
                            checks, technical inspections and quality checks in order to make sure your travels are
                            held to the highest standards.
                        </p>
                    </div>
                </div>
            </div>
            <div className="text-center">
                <h1 className="text-5xl">Service</h1>
            </div>
            <div className="container mx-auto flex flex-wrap my-8 justify-center">
                <div className="grid sm:grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        className="max-w-sm rounded overflow-hidden shadow-xl rounded card bg-indigo-800  hover:bg-indigo-900">
                        <div className="px-6 py-4 text-center text-white">
                            <FontAwesomeIcon icon={faHandPointer}  size="xl"/>
                            <h1 className="font-bold mb-2 text-center text-2xl text-white">Booking</h1>
                            <p className=" text-base">
                                Book comfortably online and check your bookings afterwards
                            </p>
                        </div>
                    </div>
                    <div
                        className="max-w-sm rounded overflow-hidden shadow-xl rounded card bg-indigo-800  hover:bg-indigo-900">
                        <div className="px-6 py-4 text-center text-white">
                            <FontAwesomeIcon icon={faSort} size="xl"/>
                            <h1 className="font-bold mb-2 text-center text-2xl text-white">Sorting</h1>
                            <p className=" text-base">
                                Sort the available trips by travel time, distance or price
                            </p>
                        </div>
                    </div>
                    <div
                        className="max-w-sm rounded overflow-hidden shadow-xl rounded card bg-indigo-800  hover:bg-indigo-900">
                        <div className="px-6 py-4 text-center text-white">
                            <FontAwesomeIcon icon={faUser}  size="xl"/>
                            <h1 className="font-bold mb-2 text-center text-2xl text-white">Customer service</h1>
                            <p className=" text-base">
                                We provide first-class customer service
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div className="text-center">
                <h1 className="text-5xl pt-10 pb-10">Planets</h1>
            </div>
            <div className="container mx-auto flex flex-wrap my-8 justify-center">
                <div className="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div
                        className="max-w-sm rounded overflow-hidden shadow-xl rounded card shadow-xl transition duration-700 ease-in-out">
                        <img className="w-full" src="/images/earth-planet.png" alt="Earth"/>
                        <div className="px-6 py-4">
                            <div className="font-bold text-xl mb-2 text-center">Earth</div>
                            <p className=" text-base">
                                One of the most visited planets in our selection. You can easily enjoy the
                                weather and nature of this planet.
                            </p>
                        </div>
                        <div className="px-6 py-4 text-center">
                                    <span
                                        className="inline-block bg-indigo-800 rounded-full px-3 py-1 text-sm font-semibold  mr-2 text-white">#Atmosphere</span>
                        </div>
                    </div>
                    <div className="max-w-sm rounded overflow-hidden shadow-xl rounded card">
                        <img className="w-full" src="/images/mars-planet.png" alt="Mars"/>
                        <div className="px-6 py-4">
                            <div className="font-bold text-xl mb-2 text-center">Mars</div>
                            <p className=" text-base">
                                The best established human colony once started by Elon Musk. You can even enjoy
                                KFC and McDonald's there.
                            </p>
                        </div>
                        <div className="px-6 py-4 text-center">
                                    <span
                                        className="inline-block bg-indigo-800 rounded-full px-3 py-1 text-sm font-semibold  mr-2 text-white">#Colony</span>
                        </div>
                    </div>
                    <div className="max-w-sm rounded overflow-hidden shadow-xl rounded card">
                        <img className="w-full" src="/images/mercury-planet.png" alt="Mercury"/>
                        <div className="px-6 py-4">
                            <div className="font-bold text-xl mb-2 text-center">Mercury</div>
                            <p className=" text-base">
                                Welcome to the smallest planet of the solar system. Enjoy long walks on the
                                extensive rocky fields of Mercury.
                            </p>
                        </div>
                        <div className="px-6 py-4 text-center">
                                    <span
                                        className="inline-block bg-indigo-800 rounded-full px-3 py-1 text-sm font-semibold  mr-2 text-white">#Hiking</span>
                        </div>
                    </div>
                </div>
            </div>
            <div className="text-center">
                <h2 className="text-xl pt-10 pb-10">and more...</h2>
            </div>
            <div className="text-center">
                <h1 className="text-5xl pt-10 pb-10">Contact Us</h1>
            </div>
            <div className=" container mx-auto flex flex-wrap mb-16 justify-center mt-8 overflow-hidden">
                <div className="grid grid-cols-1 md:grid-cols-2">
                    <div className="p-6 mr-2 bg-gray-100 bg-indigo-800 rounded-lg">
                        <h1 className="text-4xl sm:text-5xl text-white font-extrabold tracking-tight">
                            Get in touch
                        </h1>
                        <p className="text-normal text-lg sm:text-2xl font-medium text-white mt-2">
                            Fill in the form to start a conversation
                        </p>
                        <div className="flex items-center mt-8 text-white">
                            <svg fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"
                                 strokeWidth="1.5"
                                 viewBox="0 0 24 24" className="w-8 h-8 text-gray-200">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div className="ml-4 text-md tracking-wide font-semibold w-40">
                                Cosmos Odyssey Inc, Acme st. 12, Texas,
                                123456
                            </div>
                        </div>
                        <div className="flex items-center mt-4 text-white">
                            <svg fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"
                                 strokeWidth="1.5"
                                 viewBox="0 0 24 24" className="w-8 h-8 text-gray-200">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div className="ml-4 text-md tracking-wide font-semibold w-40">
                                +44 1234567890
                            </div>
                        </div>
                        <div className="flex items-center mt-2 text-white">
                            <svg fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"
                                 strokeWidth="1.5"
                                 viewBox="0 0 24 24" className="w-8 h-8 text-gray-200">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div className="ml-4 text-md tracking-wide font-semibold w-40">
                                info@cosmos.org
                            </div>
                        </div>
                        <ul className="social-icons">
                            <li><FontAwesomeIcon icon={faFacebookSquare} size="2x"/></li>
                            <li><FontAwesomeIcon icon={faTwitter} size="2x"/></li>
                            <li><FontAwesomeIcon icon={faInstagram} size="2x"/></li>
                            <li><FontAwesomeIcon icon={faLinkedin} size="2x"/></li>
                            <li><FontAwesomeIcon icon={faPinterestSquare} size="2x"/></li>
                        </ul>
                    </div>
                    <form className="p-6 mb-0 mr-2 bg-gray-200 flex flex-col justify-center rounded-lg" onSubmit={submitContactForm}>
                        <div className="flex flex-col">
                            <label htmlFor="name" className="hidden">Full Name</label>
                            <input type="name" name="name" id="name" placeholder="Full Name"
                                   className="w-100 mt-2 py-3 px-3 rounded-lg bg-white border border-gray-300  text-gray-800 font-semibold focus:border-indigo-500 focus:outline-none"
                                   value={name}
                                   onChange={onNameChange}
                                   required/>
                        </div>
                        <div className="flex flex-col mt-2">
                            <label htmlFor="email" className="hidden">Email</label>
                            <input type="email" name="email" id="email" placeholder="Email"
                                   className="w-100 mt-2 py-3 px-3 rounded-lg bg-white border border-gray-300 text-gray-800 font-semibold focus:border-indigo-500 focus:outline-none"
                                   value={email}
                                   onChange={onEmailChange}
                                   required/>
                        </div>
                        <div className="flex flex-col mt-2">
                            <label htmlFor="tel" className="hidden">Number</label>
                            <input type="tel" name="tel" id="tel" placeholder="Telephone Number"
                                   className="w-100 mt-2 py-3 px-3 rounded-lg bg-white border border-gray-300 text-gray-800 font-semibold focus:border-indigo-500 focus:outline-none"
                                   value={number}
                                   onChange={onNumberChange}
                                   required/>
                        </div>
                        <div className="flex flex-col mt-2">
                            <label htmlFor="tel" className="hidden">Message</label>
                            <textarea type="text" name="Mes" id="Mes" placeholder="Message"
                                      className="w-100 mt-2 py-3 px-3 rounded-lg bg-white border border-gray-300 text-gray-800 font-semibold focus:border-indigo-500 focus:outline-none"
                                      value={message}
                                      onChange={onMessageChange}
                                      rows="3" required></textarea>
                        </div>
                        <button type="submit"
                                className="md:w-32 bg-indigo-800 hover:bg-blue-dark text-white font-bold py-3 px-6 rounded-lg mt-3 hover:bg-indigo-500 transition ease-in-out duration-300">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </Guest>
    )
}
