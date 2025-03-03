window.addEventListener('load', function() {
    let $controlElements = document.querySelectorAll('.controlClass');
    $controlElements.forEach(function(e) {
        let value = e.value;
        let parentNode = e.parentNode;
        if(value === '') {
            parentNode.classList.add('has-error');
        } else {
            parentNode.classList.remove('has-error');
        }
    });

    let $controlButtons = document.querySelectorAll('.controlButton');
    $controlButtons.forEach($controlButton => {
        $controlButton.addEventListener('click', () => {
            let checker = $controlButton.getAttribute('data-checker');
            let meta = $controlButton.getAttribute('data-meta');
            let field = $controlButton.getAttribute('data-field');
            let ext = $controlButton.getAttribute('data-ext');
            if(checker === 'true') {
                checkExtension(meta, field, ext, $controlButton);
            } else {
                saveChanges(meta, field, $controlButton);
            }
        });
    });
}, false);

function checkExtension(metaUid, fieldId, fileExtension, controlButton) {
    let fieldRowId = document.getElementById('f'+fieldId+'_'+metaUid);
    let fieldValue = fieldRowId.value;
    if(fileExtension !== '' && fieldValue !== '') {
        let nameElements = fieldValue.split('.');
        let nameExtension = nameElements[nameElements.length - 1];
        let lowerExtension = nameExtension.toLowerCase();
        let extension = fileExtension.toLowerCase();
        if(lowerExtension !== extension) {
            alert('Bitte den Download Namen mit Dateiendung ".'+extension+'" angeben!');
        } else {
            saveChanges(metaUid, fieldId, controlButton);
        }
    } else {
        saveChanges(metaUid, fieldId, controlButton);
    }
}

function saveChanges(metaUid, fieldId, controlButton)
{
    let ajaxUrl = 'index.php?eID=changecopyrightinformation';
    let fieldRowId = document.getElementById('f'+fieldId+'_'+metaUid);
    let parentNode = fieldRowId.parentNode;
    let fieldValue = fieldRowId.value;
    let fieldName = fieldRowId.dataset['field'];
    fieldValue.trim();

    let $controlButton = document.querySelector('.controlButton');
    $controlButton.classList.remove('btn-success');
    $controlButton.classList.add('btn-primary');

    let loader = document.createElement('span');
    loader.classList.add('ic14v-loader');
    parentNode.appendChild(loader);

    let $parameters = [];
    $parameters['action'] = 'saveChanges';
    $parameters['metaUid'] = metaUid;
    $parameters['name'] = fieldName;
    $parameters['value'] = fieldValue;

    let xhr = new XMLHttpRequest();
    xhr.open('POST', ajaxUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if(xhr.readyState === 4 && xhr.status === 200) {
            let InData = JSON.parse(xhr.responseText);
            let result = InData['message'];
            if(result['done'] === true) {
                controlButton.classList.remove('btn-primary');
                controlButton.classList.add('btn-success');
                setTimeout(function() {
                    controlButton.classList.add('btn-primary');
                    controlButton.classList.remove('btn-success');
                    loader.remove();
                }, 2000);
                if(fieldValue === '') {
                    parentNode.classList.add('has-error');
                } else {
                    parentNode.classList.remove('has-error');
                }
            }
        } else {
            console.log(ajaxUrl, parameters);
            console.log(xhr);
            alert('An error occurred');
        }
    };
    let query = [];
    for(let key in $parameters) {
        query.push(encodeURIComponent(key)+'='+encodeURIComponent($parameters[key]));
    }
    xhr.send(query.join('&'));
}