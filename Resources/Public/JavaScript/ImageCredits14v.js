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

function saveChanges(metaUid, fieldId, controlButton) {
    const fieldRowId = document.getElementById(`f${fieldId}_${metaUid}`);
    if (!fieldRowId) {
        console.error('Feld nicht gefunden:', `f${fieldId}_${metaUid}`);
        return;
    }

    const parentNode = fieldRowId.parentNode;
    const fieldValue = fieldRowId.value.trim();
    const fieldName = fieldRowId.dataset.field;

    controlButton.classList.remove('btn-success');
    controlButton.classList.add('btn-primary');

    let loader = parentNode.querySelector('.ic14v-loader');
    if (!loader) {
        loader = document.createElement('span');
        loader.className = 'ic14v-loader';
        parentNode.appendChild(loader);
    }

    const params = new URLSearchParams({
        action: 'saveChanges',
        metaUid: String(metaUid),
        name: fieldName,
        value: fieldValue
    });

    fetch('/index.php?eID=imagecredits_update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data?.message?.done) {
                controlButton.classList.remove('btn-primary');
                controlButton.classList.add('btn-success');
                setTimeout(() => {
                    controlButton.classList.remove('btn-success');
                    controlButton.classList.add('btn-primary');
                    loader.remove();
                }, 2000);
                parentNode.classList.toggle('has-error', fieldValue === '');
            } else {
                throw new Error('Server-Antwort ungültig');
            }
        })
        .catch(error => {
            console.error('AJAX Fehler:', error);
            alert('Fehler beim Speichern. Bitte erneut versuchen.');
            loader.remove();
        });
}
