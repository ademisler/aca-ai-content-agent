document.addEventListener('DOMContentLoaded', function () {
    const testButton = document.getElementById('aca-test-connection');
    const statusSpan = document.getElementById('aca-test-status');
    const styleGuideButton = document.getElementById('aca-generate-style-guide');
    const styleGuideStatus = document.getElementById('aca-style-guide-status');
    const generateIdeasButton = document.getElementById('aca-generate-ideas');
    const ideasStatus = document.getElementById('aca-ideas-status');
    const ideaList = document.querySelector('.aca-idea-list');
    const generateClusterButton = document.getElementById('aca-generate-cluster');
    const suggestUpdateButtons = document.querySelectorAll('.aca-suggest-update');
    const fetchGSCButton = document.getElementById('aca-fetch-gsc');
    const gscResults = document.getElementById('aca-gsc-results');

    const validateLicenseButton = document.getElementById('aca-validate-license');
    const licenseStatusSpan = document.getElementById('aca-license-status');

    // Function to handle API requests
    const handleApiRequest = (action, data, statusElement, buttonElement) => {
        statusElement.textContent = 'Processing...';
        statusElement.style.color = '#666';
        if (buttonElement) buttonElement.disabled = true;

        const formData = new URLSearchParams();
        formData.append('action', action);
        formData.append('nonce', aca_admin_ajax.nonce);
        for (const key in data) {
            formData.append(key, data[key]);
        }

        return fetch(aca_admin_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .finally(() => {
            if (buttonElement) buttonElement.disabled = false;
        });
    };

    if (testButton) {
        testButton.addEventListener('click', () => {
            handleApiRequest('aca_test_connection', {}, statusSpan, testButton)
            .then(result => {
                if (result.success) {
                    statusSpan.textContent = result.data;
                    statusSpan.style.color = '#228B22';
                } else {
                    statusSpan.textContent = 'Error: ' + result.data;
                    statusSpan.style.color = '#DC143C';
                }
            });
        });
    }

    if (styleGuideButton) {
        styleGuideButton.addEventListener('click', () => {
            handleApiRequest('aca_generate_style_guide', {}, styleGuideStatus, styleGuideButton)
            .then(result => {
                if (result.success) {
                    styleGuideStatus.textContent = result.data;
                    styleGuideStatus.style.color = '#228B22';
                } else {
                    styleGuideStatus.textContent = 'Error: ' + result.data;
                    styleGuideStatus.style.color = '#DC143C';
                }
            });
        });
    }

    if (generateIdeasButton) {
        generateIdeasButton.addEventListener('click', () => {
            handleApiRequest('aca_generate_ideas', {}, ideasStatus, generateIdeasButton)
            .then(result => {
                if (result.success) {
                    ideasStatus.textContent = result.data.message;
                    ideasStatus.style.color = '#228B22';
                    setTimeout(() => location.reload(), 1000); // Refresh to show new ideas
                } else {
                    ideasStatus.textContent = 'Error: ' + result.data;
                    ideasStatus.style.color = '#DC143C';
                }
            });
        });
    }

    if (generateClusterButton) {
        generateClusterButton.addEventListener('click', () => {
            const topic = prompt('Enter main topic for the cluster:');
            if (!topic) return;
            handleApiRequest('aca_generate_cluster', { topic: topic }, ideasStatus, generateClusterButton)
            .then(result => {
                if (result.success) {
                    ideasStatus.textContent = 'Cluster generated.';
                    ideasStatus.style.color = '#228B22';
                } else {
                    ideasStatus.textContent = 'Error: ' + result.data;
                    ideasStatus.style.color = '#DC143C';
                }
            });
        });
    }

    if (ideaList) {
        ideaList.addEventListener('click', (e) => {
            const button = e.target;
            const listItem = button.closest('li');
            if (!listItem) return;

            const ideaId = listItem.dataset.id;

            if (button.classList.contains('aca-write-draft')) {
                const statusElement = listItem.querySelector('.aca-draft-status');
                handleApiRequest('aca_write_draft', { id: ideaId }, statusElement, button)
                .then(result => {
                    if (result.success) {
                        statusElement.innerHTML = `Success! <a href="${result.data.edit_link}" target="_blank">Edit Draft</a>`;
                        statusElement.style.color = '#228B22';
                        button.style.display = 'none'; // Hide button after success
                        listItem.querySelector('.aca-reject-idea').style.display = 'none';
                    } else {
                        statusElement.textContent = 'Error: ' + result.data;
                        statusElement.style.color = '#DC143C';
                    }
                });
            }

            if (button.classList.contains('aca-reject-idea')) {
                const statusElement = listItem.querySelector('.aca-draft-status');
                handleApiRequest('aca_reject_idea', { id: ideaId }, statusElement, button)
                .then(result => {
                    if (result.success) {
                        listItem.style.transition = 'opacity 0.5s ease';
                        listItem.style.opacity = '0';
                        setTimeout(() => listItem.remove(), 500);
                    } else {
                        statusElement.textContent = 'Error: ' + result.data;
                        statusElement.style.color = '#DC143C';
                    }
                });
            }

            if (button.classList.contains('aca-feedback-btn')) {
                const value = button.dataset.value;
                handleApiRequest('aca_submit_feedback', { id: ideaId, value: value }, button, button)
                .then(() => {
                    button.style.opacity = '0.5';
                });
            }
        });
    }

    if (validateLicenseButton) {
        validateLicenseButton.addEventListener('click', () => {
            const licenseKeyInput = document.getElementById('aca_license_key');
            const licenseKey = licenseKeyInput.value;

            handleApiRequest('aca_validate_license', { license_key: licenseKey }, licenseStatusSpan, validateLicenseButton)
            .then(result => {
                if (result.success) {
                    licenseStatusSpan.textContent = result.data;
                    licenseStatusSpan.style.color = '#228B22';
                } else {
                    licenseStatusSpan.textContent = 'Error: ' + result.data;
                    licenseStatusSpan.style.color = '#DC143C';
                }
            });
        });
    }

    if (suggestUpdateButtons.length) {
        suggestUpdateButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const postId = btn.dataset.postId;
                handleApiRequest('aca_suggest_update', { post_id: postId }, btn, btn)
                .then(result => {
                    if (result.success) {
                        alert(result.data);
                    } else {
                        alert('Error: ' + result.data);
                    }
                });
            });
        });
    }

    if (fetchGSCButton) {
        fetchGSCButton.addEventListener('click', () => {
            handleApiRequest('aca_fetch_gsc_data', {}, gscResults, fetchGSCButton)
            .then(result => {
                if (result.success) {
                    const rows = result.data.rows || [];
                    if (rows.length) {
                        const ul = document.createElement('ul');
                        rows.forEach(r => {
                            const li = document.createElement('li');
                            const query = r.keys && r.keys[0] ? r.keys[0] : '';
                            li.textContent = query + ' (' + r.clicks + ' clicks)';
                            ul.appendChild(li);
                        });
                        gscResults.innerHTML = '';
                        gscResults.appendChild(ul);
                    } else {
                        gscResults.textContent = 'No data.';
                    }
                } else {
                    gscResults.textContent = 'Error: ' + result.data;
                }
            });
        });
    }
});