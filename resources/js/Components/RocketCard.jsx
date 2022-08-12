import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"
import {
    faShuttleSpace
} from "@fortawesome/free-solid-svg-icons"

export default function RocketCard(props){
    return (
        <div className="rounded-md bg-gray-200 w-full p-3 my-4 py-5">
            <div className="w-full p-4 flex justify-center">
                <FontAwesomeIcon icon={faShuttleSpace} className="text-gray-400 text-4xl"></FontAwesomeIcon>
            </div>
            <div className="w-full flex justify-center">
                <div className="text-gray-600 font-bold pl-3">{props.text}</div>
            </div>
        </div>
    )
}
