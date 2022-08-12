import Guest from "@/Layouts/Guest"
import RocketCard from "@/Components/RocketCard"
import TripCard from "@/Components/TripCard"
import React, {useEffect, useRef} from 'react'
import {toast} from "react-toastify"

export default function BookingConfirmed(props) {

    const isMounted = useRef(false)

    useEffect(() => {
        if(!isMounted.current){
            isMounted.current = true
            let showSuccessMessage = localStorage.getItem("booking-confirm-msg-" + props.booking.id)
            if(showSuccessMessage){
                toast.success("Booking confirmed")
                localStorage.removeItem("booking-confirm-msg-" + props.booking.id)
            }
        }
    }, [])


    return (
        <Guest title={props.title}>
                {!props.error ? (
                    <div className="px-5 sm:px-0 sm:w-4/5 lg:w-3/5 mx-auto">
                        <div className="max-w-7xl mx-auto my-5">
                            <h1 className="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span className="block xl:inline">Your booking</span>
                            </h1>
                        </div>
                        <div className="flex flex-col">
                            <div className="flex flex-col mb-3">
                                <span className="mr-2 font-bold text-lg">Booking id:</span>
                                <span className="mr-2 text-lg">{props.booking.id}</span>
                            </div>
                            <div className="flex flex-col mb-3">
                                <span className="mr-2 font-bold text-lg">First name:</span>
                                <span className="mr-2 text-lg">{props.booking.firstname}</span>
                            </div>
                            <div className="flex flex-col mb-3">
                                <span className="mr-2 font-bold text-lg">Last name:</span>
                                <span className="mr-2 text-lg">{props.booking.lastname}</span>
                            </div>
                        </div>
                        <div className="flex flex-col">
                            <TripCard trip={props.trip} expanded={true} displayBookButton={false} hideBookButton={true} ></TripCard>
                        </div>
                    </div>
                ) : (
                    <div className="px-5 sm:px-0 sm:w-4/5 lg:w-3/5 mx-auto">
                        <div className="max-w-7xl mx-auto my-5">
                            <h1 className="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span className="block xl:inline">Your booking</span>
                            </h1>
                        </div>
                        <RocketCard text={props.error}/>
                    </div>
                )}
        </Guest>
    )
}
