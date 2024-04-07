import validator from "validator";

export function validateShortAnswer(validation, input){
    console.log(validation);
    if(validation.type === "NUMBER"){
         return   validateNumber(validation, input);
    }
    else if(validation.type === "TEXT"){
        return validateText(validation, input);
    }
    else if(validation.type === "LENGTH"){
        return validateLength(validation, input);
    }
    else if(validation.type === "REGULAR_EXPRESSION"){
        return validateRegexp(validation, input);
    }
    else if(validation.rule === "OPTION"){
        return validateOptions(validation, input);
    }
    else if(validation.type === "checkboxes"){
        return validateCheckboxes(validation, input);
    }
}

export function validateNumber(validation, input){
    if(validator.isNumeric(input)){
            if(validation.rule === "GREATER_THAN"){
                return   parseFloat(input) > parseFloat(parseFloat(validation.value)) ? true : false;
            }
            else if(validation.rule === "GREATER_THAN_EQUAL_TO"){
                return   parseFloat(input) >= parseFloat(validation.value) ? true : false;
            }
            else if(validation.rule === "LESS_THAN"){
                return   parseFloat(input) < parseFloat(validation.value) ? true : false;
            }
            else if(validation.rule === "LESS_THAN_EQUAL_TO"){
                return   parseFloat(input) <= parseFloat(validation.value) ? true : false;
            }
            else if(validation.rule === "EQUAL_TO"){
                return   parseFloat(input) === parseFloat(validation.value) ? true : false;
            }
            else if(validation.rule === "NOT_EQUAL_TO"){
                return   parseFloat(input) !== parseFloat(validation.value) ? true : false;
            }
            else if(validation.rule === "BETWEEN"){
                return   (parseFloat(input) > parseFloat(validation.value) && parseFloat(input) < parseFloat(validation.value_2)) ? true : false;
            }
            else if(validation.rule === "NOT_BETWEEN"){
                return   ((parseFloat(input) < parseFloat(validation.value) ) || (parseFloat(input) > parseFloat(validation.value_2))) ? true : false;
            }
            else if(validation.rule === "IS_NUMBER"){
                return   validator.isNumeric(input) ? true : false;
            }
            else if(validation.rule === "WHOLE_NUMBER"){
                return  (parseFloat(input) - Math.floor(input)) === 0 ? true : false;
            }
    }
    else{
        return false;
    }
}

export function validateText(validation, input){
    if(validation.rule === "CONTAINS"){
            return validator.contains(input, validation.value);
        }
    else if(validation.rule === "NOT_CONTAINS"){
            return !validator.contains(input, validation.value);
        }
    else if(validation.rule === "EMAIL"){
        return validator.isEmail(input);
    }
    else if(validation.rule === "URL"){
        return validator.isURL(input);
    }
}

export function validateLength(validation, input){
    if(validation.rule === "MAX_CHAR_COUNT"){
        return validator.isLength(input, {max:validation.value});
    }
    else if(validation.rule === "MIN_CHAR_COUNT"){
        return validator.isLength(input, {min:validation.value});
    }
}

export function validateRegexp(validation, input){
    if(validation.rule === "CONTAINS"){
        return validator.matches(input, validation.value);

    }
    else if(validation.rule === "NOT_CONTAINS"){
        return !validator.matches(input, validation.value);

    }
    else if(validation.rule === "MATCHES"){
            return validator.matches(input, validation.value);
        }
    else if(validation.rule === "NOT_MATCHES"){
        return !validator.matches(input, validation.value);

    }
}

export function validateOptions(validation, input){
    console.log(input);
    if(validation.type === "AT_LEAST"){
        return input.length >=   parseInt(validation.value);
    }
    else if(validation.type === "AT_MOST"){
        console.log(input.length);
        return input.length <= parseInt(validation.value);
    }
    else if(validation.type === "EXACTLY"){
        return input.length ===  parseInt(validation.value);
    }
}

export function validateCheckboxes(validation, input){
    console.log(input);
    if(validation.rule === "AT_LEAST"){
        return input.length >=   parseInt(validation.value);
    }
    else if(validation.rule === "AT_MOST"){
        console.log(input.length);
        return input.length <= parseInt(validation.value);
    }
    else if(validation.rule === "EXACTLY"){
        return input.length ===  parseInt(validation.value);
    }
}

export function shuffleArray(array) {
    let currentIndex = array.length,  randomIndex;
  
    // While there remain elements to shuffle.
    while (currentIndex != 0) {
  
      // Pick a remaining element.
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex--;
  
      // And swap it with the current element.
      [array[currentIndex], array[randomIndex]] = [
        array[randomIndex], array[currentIndex]];
    }
  
    return array;
  }