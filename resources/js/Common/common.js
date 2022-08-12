
const selectStyles = {
    input: (base) => ({
        ...base,
        'input:focus': {
            boxShadow: 'none',
        },
    }),
    control: (base, state) => ({
        ...base,
        '&:hover': {borderColor: 'gray'}, // border style on hover
        border: '1px solid lightgray', // default border color
        boxShadow: 'none', // no box-shadow
    })
}

/**
 * Get saved option from local storage and parse json
 * @param   {string} key
 * @param   {array} array
 * @return  {object}
 */
function getSavedOptionIfInArray(key, array) {
    let optionString = localStorage.getItem(key)
    if (optionString && _.isString(optionString)) {
        try {
            let option = JSON.parse(optionString)
            if(array.some(arr_opt => arr_opt.value = option.value)){
                return option
            }else{
                return null
            }
        } catch (e) {
            return null
        }
    }
    return null
}

/**
 * Save object to local storage
 * @param   {string} key
 * @param   {object } option
 * @return  {object}
 */
function setSavedOption(key, option) {
    if (option) {
        localStorage.setItem(key, JSON.stringify(option))
    } else {
        localStorage.removeItem(key)
    }
}

/**
 * Save object to local storage if in provided array
 * @param   {string} key
 * @param   {string} saveKey
 * @param   {object} optionsArray
 * @return  {boolean} If object from array was saved or not
 */
function saveOptionIfInArray(key, saveKey, optionsArray) {
    if (!key) {
        return false
    }
    let option = optionsArray.find(option => option.value === key)
    if (!option) {
        return false
    }
    localStorage.setItem(saveKey, JSON.stringify(option))
    return true
}

export { selectStyles, getSavedOptionIfInArray, setSavedOption, saveOptionIfInArray };
