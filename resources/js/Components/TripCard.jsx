import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"
import {faArrowCircleDown, faArrowCircleUp, faArrowsH, faClock, faRocket} from "@fortawesome/free-solid-svg-icons"
import {useState} from "react"

export default function TripCard(props){

    const [detailsOpen, setDetailsOpen] = useState(!!props.expanded)

    /**
     * Get duration string to display
     * @param   {int} durationSeconds
     * @param   {boolean} displayMinutes
     * @return  {string}
     */
    function getDurationString(durationSeconds, displayMinutes = false){
        let humanizedDuration = ""
        if(durationSeconds > (60*60*24)){
            let days = Math.floor(durationSeconds /  (60*60*24))
            humanizedDuration += days + "d "
            durationSeconds -= days * (60*60*24)
        }
        if(durationSeconds > 60*60){
            let hours = Math.floor(durationSeconds / (60*60))
            humanizedDuration += hours + "h "
            durationSeconds -= hours * (60*60)
        }
        if(displayMinutes && durationSeconds > 60){
            let minutes = Math.floor(durationSeconds / 60)
            humanizedDuration += minutes + "m"
        }
        return humanizedDuration
    }

    /**
     * Get company names string
     * @param   {object} providers
     * @return  {string}
     */
    function getCompanyNamesString(providers){
        var companyNames = providers.map(provider => provider.company_name)
        let uniqueNames = [...new Set(companyNames)]
        if(uniqueNames.length > 1){
            return uniqueNames[0] + ", +" + (uniqueNames.length - 1)
        }else{
            return uniqueNames[0]
        }
    }

    /**
     * Get stops amount string
     * @param   {object} providers
     * @return  {string}
     */
    function getStopsString(providers){
        if(providers.length > 2){
            return `${providers.length-1} stops`
        }else if(providers.length > 1){
            return "1 stop"
        }else{
            return ""
        }
    }

    /**
     * Get time between providers
     * @param   {object} provider
     * @param   {object} provider2
     * @return  {string}
     */
    function getTimeBetweenProviders(provider, provider2){
        return getDurationString(moment(provider2.flight_start).diff(moment(provider.flight_end), "second"), true)
    }

    /**
     * Get duration of provier
     * @param   {object} provider
     * @return  {string}
     */
    function getProviderDuration(provider){
        return getDurationString(moment(provider.flight_end).diff(moment(provider.flight_start), "second"), true)
    }

    /**
     * Get trip list data to be displayed
     * @param   {object} providers
     * @return  {object}
     */
    function getTripListData(providers){
        var tripListData = []
        var key = 0
        for(let $i = 0; $i < providers.length; $i++){
            tripListData.push(
                {
                    "type": "route",
                    "provider": providers[$i],
                    "key": key++
                }
            )
            if($i + 1 < providers.length){
                tripListData.push({
                    "type": "text",
                    "text": "Waiting time: " + getTimeBetweenProviders(providers[$i], providers[$i+1]),
                    "key": key++
                })
            }else{
                tripListData.push({
                    "type": "text",
                    "text": "Trip finished",
                    "key": key++
                })
            }
        }
        return tripListData
    }

    /**
     * Invert details open
     * @return  {void}
     */
    function invertDetailsOpen(){
        setDetailsOpen(!detailsOpen)
    }

    /**
     * When book button pressed
     * @return  {void}
     */
    function onBookButtonPress(){
        props.onBookButtonPress(props.trip)
    }

    return (
        <div className="rounded-md bg-gray-200 w-full p-3 my-4">
            <div className="w-full p-4 flex justify-between flex-wrap">
                <div className="flex-1 flex flex-row items-center justify-between">
                    <div>
                        <div className="flex items-center mb-1">
                            <div className="flex flex-nowrap items-center">
                                <FontAwesomeIcon icon={faArrowCircleUp}  size="sm" className="mr-2"/>
                                <div className="font-bold mr-1">Departure:</div>
                            </div>
                            <div className="whitespace-nowrap">{moment(props.trip.start).format('HH:mm DD.MM.YYYY')}</div>
                        </div>
                        <div className="flex items-center mb-1">
                            <div className="flex flex-nowrap items-center">
                                <FontAwesomeIcon icon={faArrowCircleDown}  size="sm" className="mr-2"/>
                                <div className="font-bold mr-1">Arrival:</div>
                            </div>
                            <div>{moment(props.trip.end).format('HH:mm DD.MM.YYYY')}</div>
                        </div>
                        <div className="flex items-center mb-1">
                            <div className="flex flex-nowrap items-center">
                                <FontAwesomeIcon icon={faClock}  size="sm" className="mr-2"/>
                                <div className="font-bold mr-1">Duration:</div>
                            </div>
                            <div>{getDurationString(props.trip.duration)}</div>
                        </div>
                        <div className="flex flex-nowrap items-center">
                            <div className="flex flex-nowrap items-center">
                                <FontAwesomeIcon icon={faArrowsH}  size="sm" className="mr-2"/>
                                <div className="font-bold mr-1">Distance:</div>
                            </div>
                            <div>{props.trip.distance}</div>
                        </div>
                    </div>
                </div>
                <div className="flex-1 flex flex-col-reverse items-center hidden md:flex">
                    <div className="text-l italic">
                        {getStopsString(props.trip.providers)}
                    </div>
                </div>
                <div className="flex-1 flex items-center font justify-end">
                    <div className="mx-4 italic text-m">
                        {getCompanyNamesString(props.trip.providers)}
                    </div>
                    <div className="flex items-center flex-col">
                        <div className="text-2xl mb-2">€{props.trip.price.toFixed(2)}</div>
                        <div className="rounded-md shadow" style={{display: props.displayBookButton ? 'flex' : 'none'}}>
                            <a onClick={onBookButtonPress}
                               className="flex items-center justify-center px-3 py-1 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-500 cursor-pointer">
                                <FontAwesomeIcon icon={faRocket}  size="sm" className="mr-2"/>
                                Book
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div className="w-full flex justify-center border-t border-gray-400 pt-3">
                <div className="font-bold pl-3 cursor-pointer select-none" style={{display: props.hideBookButton ? 'none' : 'block'}} onClick={invertDetailsOpen}>Details {detailsOpen ? "-" : "+"}</div>
            </div>
            <div className={`px-10 tripcard-details ${detailsOpen ? '' : 'tripcard-details-hide'}`}>
                <ol className="border-l border-gray-500">
                    {getTripListData(props.trip.providers).map(listItem => listItem.type === 'route' ?
                        ( <li key={listItem.key}>
                            <div className="flex flex-start items-center pt-3">
                                <div className="bg-gray-500 w-2 h-2 rounded-full -ml-1 mr-3"></div>
                                <p className="text-gray-700 text-sm">
                                    <span className="mr-1 font-bold">{moment(listItem.provider.flight_start).format('HH:mm')}</span>
                                    <span className="mr-1">{moment(listItem.provider.flight_start).format('DD.MM.YYYY')}</span>
                                    <span className="mr-1">-</span>
                                    <span className="mr-1 font-bold">{moment(listItem.provider.flight_end).format('HH:mm')}</span>
                                    <span className="mr-1">{moment(listItem.provider.flight_end).format('DD.MM.YYYY')}</span>
                                    <span className="italic">({getProviderDuration(listItem.provider)})</span>
                                </p>
                            </div>
                            <div className="mt-0.5 ml-4 mb-6">
                                <h4 className="text-gray-800 font-semibold text-xl mb-1.5">{listItem.provider.from_planet_name} -> {listItem.provider.to_planet_name}</h4>
                                <p className="text-gray-500 mb-3">{listItem.provider.company_name}</p>
                            </div>
                        </li>)
                            :
                        (<li key={listItem.key}>
                            <div className="flex flex-start items-center pt-2 mb-4">
                                <div className="bg-gray-500 w-2 h-2 rounded-full -ml-1 mr-3"></div>
                                <p className="text-gray-700 text-sm font-bold">{listItem.text}</p>
                            </div>
                        </li>)
                    )}
                </ol>
            </div>
        </div>
    )
}
