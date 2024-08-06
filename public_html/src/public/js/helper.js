/**
 * Process the response and take the relevant action
 * @param {*} data 
 */
function processResponse(data) {
    if (data.httpCode != '200') 
        showMessage(data.message);
    else {
        switch (data.action) {
            case 'redirect':
                window.location.replace(data.url);
                break;
        }
    }
}