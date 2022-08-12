import Guest from "@/Layouts/Guest"
import RocketCard from "@/Components/RocketCard"
import TripCard from "@/Components/TripCard"
import React, {useEffect, useState, useRef} from 'react'
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"
import {faCircleExclamation, faSearch} from "@fortawesome/free-solid-svg-icons"
import LoaderAnimation from "@/Components/LoaderAnimation"
import {faCalendarCheck} from "@fortawesome/free-regular-svg-icons"
import {Inertia} from "@inertiajs/inertia"
import {toast} from "react-toastify"

export default function BookingConfirm(props) {

    const [requestRunning, setRequestRunning] = useState(false)
    const [errorMessage, setErrorMessage] = useState(null)
    const [firstName, setFirstName] = useState("")
    const [lastName, setLastName] = useState("")

    /**
     * Callback of first name change
     * @param   {object} event change event
     * @return  {void}
     */
    function handleFirstNameChange(event){
        setFirstName(event.target.value)
    }

    /**
     * Callback of last name change
     * @param   {object} event change event
     * @return  {void}
     */
    function handleLastNameChange(event){
        setLastName(event.target.value)
    }

    /**
     * Confirm booking to backend
     * @return  {void}
     */
    async function confirmBooking() {

        if(!firstName){
            setErrorMessage("Please enter first name!")
            return
        }

        if(!lastName){
            setErrorMessage("Please enter last name!")
            return
        }

        try{
            setRequestRunning(true)

            const res = await axios.post('/api/booking-confirm/' + props.trip_id, {
                first_name: firstName,
                last_name: lastName
            })

            if(res.data.id){
                localStorage.setItem("booking-confirm-msg-"+res.data.id, true)
                Inertia.visit("/booking-confirmed/" + res.data.id, {})
            }else{
                setErrorMessage("Unknown error occured")
            }

        }catch (error){
            if (error.response?.data && error.response?.data?.error) {
                toast.error(error.response.data.error)
            }else{
                toast.error("Internal server error")
            }
        }finally {
            setRequestRunning(false)
        }
    }

    return (
        <Guest title={props.title}>
                {!props.error ? (
                    <div className="px-5 sm:px-0 sm:w-4/5 lg:w-3/5 mx-auto">
                        <div className="max-w-7xl mx-auto my-5">
                            <h1 className="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span className="block xl:inline">Confirm booking</span>
                            </h1>
                        </div>
                        <div className="bg-red-100 rounded-lg py-5 px-6 mb-4 text-base text-red-700 mb-3" role="alert"
                             style={{display: errorMessage ? 'block' : 'none'}}>
                            <FontAwesomeIcon icon={faCircleExclamation} className="mr-2"></FontAwesomeIcon> {errorMessage}
                        </div>
                        <div className="flex flex-col pl-2">
                            <div className="flex flex-col mb-3">
                                <span className="mr-2 font-bold text-lg">First name:</span>
                                <input type="text" name="name" className="rounded w-80" id="confirm-first-name-input"
                                       autoComplete="given-name" value={firstName} onChange={handleFirstNameChange}/>
                            </div>
                            <div className="flex flex-col mb-3">
                                <span className="mr-2 font-bold text-lg">Last name:</span>
                                <input type="text" name="name" className="rounded w-80" id="confirm-last-name-input"
                                       autoComplete="family-name"  value={lastName} onChange={handleLastNameChange}/>
                            </div>
                        </div>
                        <div className="rounded-md shadow my-6" style={{display: !requestRunning ? 'flex' : 'none'}}>
                            <a onClick={confirmBooking} className="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                    <FontAwesomeIcon icon={faCalendarCheck} size="sm" className="mr-2 pb-1"/>
                            Confirm booking
                            </a>
                        </div>
                        <div className="w-full flex items-center flex-col content-center justify-around" style={{display: requestRunning ? 'flex' : 'none'}}>
                            <LoaderAnimation/>
                            <div className="py-3">Loading...</div>
                        </div>
                        <div className="flex flex-col">
                            <TripCard trip={props.trip} expanded={true} displayBookButton={false} hideBookButton={true}></TripCard>
                        </div>
                    </div>

                ) : (
                    <div className="px-5 sm:px-0 sm:w-4/5 lg:w-3/5 mx-auto">
                        <div className="max-w-7xl mx-auto my-5">
                            <h1 className="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span className="block xl:inline">Confirm booking</span>
                            </h1>
                        </div>
                        <RocketCard text={props.error}/>
                    </div>
                    )}
        </Guest>
    )
}
