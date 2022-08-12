import Guest from "@/Layouts/Guest"
import TripCard from "@/Components/TripCard"
import {
    faBuilding,
    faCircleArrowDown,
    faCircleArrowUp, faCircleExclamation,
    faSearch,
    faSort
} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"
import Select from 'react-select'
import React, {useEffect, useState, useRef} from 'react'
import {Inertia} from '@inertiajs/inertia'
import RocketCard from "@/Components/RocketCard"
import LoaderAnimation from "@/Components/LoaderAnimation"
import {toast} from "react-toastify"
import {selectStyles, getSavedOptionIfInArray, setSavedOption, saveOptionIfInArray} from "@/Common/common"

export default function Booking(props) {

    const isMounted = useRef(false)

    // Prepare planet options
    let planetOptions = []
    if (props.planets && props.planets.length > 0) {
        planetOptions = props.planets.map(planet => {
            return {
                value: planet.name,
                label: planet.name
            }
        })
    }

    // Prepare company options
    let companyOptions = []
    if (props.companies && props.companies.length > 0) {
        companyOptions = props.companies.map(company => {
            return {
                value: company.name,
                label: company.name
            }
        })
    }

    var sortOptions = [
        {value: "best", label: "Best"},
        {value: "price", label: "Price"},
        {value: "distance", label: "Distance"},
        {value: "duration", label: "Duration"},
    ]

    // Get from & to planets from props
    let doTripsSearch = true
    doTripsSearch = doTripsSearch && saveOptionIfInArray(props.search?.fromPlanetName, "fromPlanetOption", planetOptions)
    doTripsSearch = doTripsSearch && saveOptionIfInArray(props.search?.toPlanetName, "toPlanetOption", planetOptions)

    // Get company and sort filters from props, if planets were defined
    let companyOptionInit = null
    let sortOptionInit = null
    if (doTripsSearch) {
        let companies = props.search?.companies ? props.search?.companies.split(",") : []
        companyOptionInit = companyOptions.filter(companyOption => companies.includes(companyOption.value))
        sortOptionInit = sortOptions.find(sortOption => sortOption.value === props.search?.sort) ??
            sortOptions.find(sortOption => sortOption.value === "best")
    }

    // Select options
    const [fromPlanetOption, setFromPlanetOption] = useState(getSavedOptionIfInArray('fromPlanetOption', planetOptions))
    const [toPlanetOption, setToPlanetOption] = useState(getSavedOptionIfInArray('toPlanetOption', planetOptions))
    const [selectedCompanyOptions, setSelectedCompanyOptions] = useState(companyOptionInit)
    const [sortOption, setSortOption] = useState(sortOptionInit)

    // UI state
    const [filteringTrips, setFilteringTrips] = useState(localStorage.getItem("booking-filtering-sorting") === "true")
    const [loadingTrips, setLoadingTrips] = useState(doTripsSearch)
    const [errorMessage, setErrorMessage] = useState(null)

    // Data
    const [trips, setTrips] = useState(null)


    /**
     * Handle change of from planet
     * @param   {object} selectedOption
     * @return  {void}
     */
    function handleChangeFromPlanet(selectedOption) {
        setSavedOption("fromPlanetOption", selectedOption)
        setFromPlanetOption(selectedOption)
    }


    /**
     * Handle change of to planet
     * @param   {object} selectedOption
     * @return  {void}
     */
    function handleChangeToPlanet(selectedOption) {
        setSavedOption("toPlanetOption", selectedOption)
        setToPlanetOption(selectedOption)
    }


    /**
     * Handle change of company
     * @param   {object} selectedOption
     * @return  {void}
     */
    function handleChangeCompany(selectedOption) {
        setSelectedCompanyOptions(selectedOption)
    }


    /**
     * Handle change of sort
     * @param   {object} selectedOption
     * @return  {void}
     */
    function handleChangeSort(selectedOption) {
        setSortOption(selectedOption)
    }



    /**
     * Navigate to search page
     * @param   {object} event Is not null when search button clicked
     * @return  {void}
     */
    function navigateToSearchPage(event = null) {

        setErrorMessage(null)

        let searchParams = new URLSearchParams()
        let searchFromPlanet, searchToPlanet
        if (event) {
            if (!fromPlanetOption || !fromPlanetOption.value) {
                setErrorMessage("Please select source planet")
                return
            }

            if (!toPlanetOption || !toPlanetOption.value) {
                setErrorMessage("Please select destination planet")
                return
            }

            if (toPlanetOption.value === fromPlanetOption.value) {
                setErrorMessage("Source and destination planets cannot be the same")
                return
            }

            searchFromPlanet = fromPlanetOption.value
            searchToPlanet = toPlanetOption.value
        } else {
            localStorage.setItem("booking-filtering-sorting", "true")

            if (sortOption) {
                searchParams.append("sort", sortOption.value)
            }
            if (selectedCompanyOptions && selectedCompanyOptions.length > 0) {
                searchParams.append("companies", selectedCompanyOptions.map(e => e.value).join(","))
            }

            searchFromPlanet = props.search.fromPlanetName
            searchToPlanet = props.search.toPlanetName
        }

        if (searchParams.toString().length > 0) {
            Inertia.visit(`/booking/${searchFromPlanet}/${searchToPlanet}?${searchParams.toString()}`, {})
        } else {
            Inertia.visit(`/booking/${searchFromPlanet}/${searchToPlanet}`, {})
        }
    }


    /**
     * Get trips based on parameters from backend
     * @return  {void}
     */
    async function getTrips() {

        let filtersOrSort = localStorage.getItem("booking-filtering-sorting") === "true"
        localStorage.removeItem("booking-filtering-sorting")

        let params = {
            fromPlanetName: filtersOrSort ? props.search?.fromPlanetName : fromPlanetOption.value,
            toPlanetName: filtersOrSort ? props.search?.toPlanetName : toPlanetOption.value,
        }

        if (props.search?.companies) {
            params.companies = props.search?.companies
        }

        if (props.search?.sort) {
            params.sort = props.search?.sort
        }

        setFilteringTrips(filtersOrSort)
        setLoadingTrips(true)

        try {
            const res = await axios.get('/api/search-trips', {
                params: params
            })

            setTrips(res.data)

        } catch (error) {
            if (error.response.data && error.response.data.error) {
                setErrorMessage(error.response.data.error)
            } else {
                setErrorMessage("Internal server error")
            }
        }

        setFilteringTrips(false)
        setLoadingTrips(false)
    }

    /**
     * Callback to TripCard Book button press
     * @param   {object} trip Contains basic info about trip
     * @return  {void}
     */
    async function selectTrip(trip) {
        try {
            const res = await axios.post('/api/select-trip', {
                ...trip
            })

            if (res.data.id) {
                Inertia.visit("/booking-confirm/" + res.data.id, {preserveState: false})
            }

        } catch (error) {
            if (error.response?.data?.error) {
                toast.error(error.response.data.error)
            } else {
                toast.error("Internal server error")
            }
        }
    }

    useEffect(() => {
        if (isMounted.current) {
            navigateToSearchPage()
        }
    }, [sortOption, selectedCompanyOptions])

    useEffect(() => {
        isMounted.current = true
        if (doTripsSearch) {
            getTrips()
        }
    }, [])


    return (
        <Guest title={props.title}>
            <div className="px-5 sm:px-0 sm:w-4/5 lg:w-3/5 mx-auto">
                <div className="max-w-7xl mx-auto my-5">
                    <h1 className="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span className="block xl:inline">Book your flight here</span>
                    </h1>
                </div>
                <div className="bg-red-100 rounded-lg py-5 px-6 mb-4 text-base text-red-700 mb-3" role="alert"
                     style={{display: errorMessage ? 'block' : 'none'}}>
                    <FontAwesomeIcon icon={faCircleExclamation} className="mr-2"></FontAwesomeIcon> {errorMessage}
                </div>
                <div className="flex text-xl flex-wrap">
                    <div className="flex mr-4 py-2">
                        <div className="mr-2 flex items-center">
                            <FontAwesomeIcon icon={faCircleArrowUp} size="sm" className="mr-2"/>
                            Source:
                        </div>
                        <Select options={planetOptions} className="w-52" styles={selectStyles}
                                onChange={handleChangeFromPlanet} defaultValue={fromPlanetOption}/>
                    </div>
                    <div className="flex mr-4 py-2">
                        <div className="mr-2 flex items-center">
                            <FontAwesomeIcon icon={faCircleArrowDown} size="sm" className="mr-2"/>
                            Destination:
                        </div>
                        <Select options={planetOptions} className="w-52" styles={selectStyles}
                                onChange={handleChangeToPlanet} defaultValue={toPlanetOption}/>
                    </div>
                </div>
                <div className="flex text-xl my-3 hidden">
                    <div className="mr-2 flex items-center">
                        <i className="fa fa-calendar mr-2" aria-hidden="true"></i>
                        Date:
                    </div>
                    <input type="text"
                           id="floatingInput"
                           className="form-control block px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                           placeholder="Select a date"/>
                </div>
                <div style={{display: !trips && !loadingTrips ? 'flex' : 'none'}}>
                    <RocketCard text="8 planets of the solar system await you"></RocketCard>
                </div>
                <div className="rounded-md shadow my-4"
                     style={{display: !loadingTrips || filteringTrips ? 'flex' : 'none'}}>
                    <a onClick={navigateToSearchPage}
                       className="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                        <FontAwesomeIcon icon={faSearch} size="sm" className="mr-2 pb-1"/>
                        Find flights
                    </a>
                </div>

                <div className="w-full flex items-center flex-col content-center justify-around my-20"
                     style={{display: loadingTrips && !filteringTrips ? 'flex' : 'none'}}>
                    <LoaderAnimation/>
                    <div className="py-4">Loading...</div>
                </div>

                <div className="flex text-xl flex-wrap justify-between"
                     style={{display: trips || filteringTrips ? 'flex' : 'none'}}>
                    <div className="flex mr-4 my-2">
                        <div className="mr-2 flex items-center">
                            <FontAwesomeIcon icon={faBuilding} size="sm" className="mr-2"/>
                            Company:
                        </div>
                        <Select options={companyOptions} className="w-full min-w-60 items-center" styles={selectStyles}
                                onChange={handleChangeCompany} defaultValue={selectedCompanyOptions} isMulti={true}/>
                    </div>
                    <div className="flex my-2">
                        <div className="mr-2 flex items-center whitespace-nowrap">
                            <FontAwesomeIcon icon={faSort} size="sm" className="mr-2"/>
                            Sort by:
                        </div>
                        <Select options={sortOptions} className="w-52" styles={selectStyles} isSearchable={false}
                                onChange={handleChangeSort} defaultValue={sortOption}/>
                    </div>
                </div>
                <div className="w-full flex items-center flex-col content-center justify-around my-10"
                     style={{display: filteringTrips ? 'flex' : 'none'}}>
                    <LoaderAnimation/>
                    <div className="py-4">Loading...</div>
                </div>
                <div className="flex flex-col">
                    {trips && trips.length > 0 ?
                        trips.map(trip =>
                            <TripCard key={trip.id} trip={trip} expanded={false} displayBookButton={true}
                                      onBookButtonPress={selectTrip}></TripCard>
                        ) : <div></div>
                    }
                    {trips && trips.length === 0 ?
                        <RocketCard text="No results found. Refine your search of try again later."></RocketCard>
                        : ""}
                    {trips && trips.length === 30 ?
                        <div className="w-full text-center py-4">A maximum of 30 results are displayed</div>
                        : ""}
                </div>
            </div>
        </Guest>
    )
}
