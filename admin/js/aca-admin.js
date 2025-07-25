document.addEventListener('DOMContentLoaded', function () {
    const testButton = document.getElementById('aca-ai-content-agent-test-connection');
    const statusSpan = document.getElementById('aca-ai-content-agent-test-status');
    const styleGuideButton = document.getElementById('aca-ai-content-agent-generate-style-guide');
    const styleGuideStatus = document.getElementById('aca-ai-content-agent-style-guide-status');
    const generateIdeasButton = document.getElementById('aca-ai-content-agent-generate-ideas');
    const ideasStatus = document.getElementById('aca-ai-content-agent-ideas-status');
    const ideaList = document.querySelector('.aca-ai-content-agent-idea-list');
    const generateClusterButton = document.getElementById('aca-ai-content-agent-generate-cluster');
    const clusterTopicInput = document.getElementById('aca-ai-content-agent-cluster-topic');
    const clusterStatus = document.getElementById('aca-ai-content-agent-cluster-status');
    const suggestUpdateButtons = document.querySelectorAll('.aca-ai-content-agent-suggest-update');
    const fetchGSCButton = document.getElementById('aca-ai-content-agent-fetch-gsc');
    const generateGSCIdeasButton = document.getElementById('aca-ai-content-agent-generate-gsc-ideas');
    const gscResults = document.getElementById('aca-ai-content-agent-gsc-results');

    const validateLicenseButton = document.getElementById('aca-ai-content-agent-validate-license');
    const licenseStatusSpan = document.getElementById('aca-ai-content-agent-license-status');

    // Function to handle API requests
    const handleApiRequest = (action, data, statusElement, buttonElement) => {
        statusElement.textContent = 'Processing...';
        statusElement.style.color = '#666';
        if (buttonElement) buttonElement.disabled = true;

        const formData = new URLSearchParams();
        formData.append('action', action);
        formData.append('nonce', aca_ai_content_agent_admin_ajax.nonce);
        for (const key in data) {
            formData.append(key, data[key]);
        }

        return fetch(aca_ai_content_agent_admin_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .catch(error => {
            statusElement.textContent = 'Network error: ' + error.message;
            statusElement.style.color = '#DC143C';
            return { success: false, data: error.message };
        })
        .finally(() => {
            if (buttonElement) buttonElement.disabled = false;
        });
    };

    if (testButton) {
        testButton.addEventListener('click', () => {
            handleApiRequest('aca_ai_content_agent_test_connection', {}, statusSpan, testButton)
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
            handleApiRequest('aca_ai_content_agent_generate_style_guide', {}, styleGuideStatus, styleGuideButton)
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
            handleApiRequest('aca_ai_content_agent_generate_ideas', {}, ideasStatus, generateIdeasButton)
            .then(result => {
                if (result.success) {
                    ideasStatus.textContent = result.data.message;
                    ideasStatus.style.color = '#228B22';
                    if (result.data.ideas_html) {
                        ideaList.insertAdjacentHTML('afterbegin', result.data.ideas_html);
                    }
                } else {
                    ideasStatus.textContent = 'Error: ' + result.data;
                    ideasStatus.style.color = '#DC143C';
                }
            });
        });
    }

    if (generateClusterButton) {
        generateClusterButton.addEventListener('click', () => {
            const topic = clusterTopicInput ? clusterTopicInput.value.trim() : '';
            if (!topic) {
                alert('Please enter a topic');
                return;
            }
            handleApiRequest('aca_ai_content_agent_generate_cluster', { topic: topic }, clusterStatus, generateClusterButton)
            .then(result => {
                if (result.success) {
                    clusterStatus.textContent = 'Cluster generated.';
                    clusterStatus.style.color = '#228B22';
                } else {
                    clusterStatus.textContent = 'Error: ' + result.data;
                    clusterStatus.style.color = '#DC143C';
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

            if (button.classList.contains('aca-ai-content-agent-write-draft')) {
                const statusElement = listItem.querySelector('.aca-ai-content-agent-draft-status');
                handleApiRequest('aca_ai_content_agent_write_draft', { id: ideaId }, statusElement, button)
                .then(result => {
                    if (result.success) {
                        statusElement.innerHTML = `Success! <a href="${result.data.edit_link}" target="_blank">Edit Draft</a>`;
                        statusElement.style.color = '#228B22';
                        button.style.display = 'none'; // Hide button after success
                        listItem.querySelector('.aca-ai-content-agent-reject-idea').style.display = 'none';
                    } else {
                        statusElement.textContent = 'Error: ' + result.data;
                        statusElement.style.color = '#DC143C';
                    }
                });
            }

            if (button.classList.contains('aca-ai-content-agent-reject-idea')) {
                const statusElement = listItem.querySelector('.aca-ai-content-agent-draft-status');
                handleApiRequest('aca_ai_content_agent_reject_idea', { id: ideaId }, statusElement, button)
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

            if (button.classList.contains('aca-ai-content-agent-feedback-btn')) {
                const value = button.dataset.value;
                const feedbackButtons = listItem.querySelectorAll('.aca-ai-content-agent-feedback-btn');
                handleApiRequest('aca_ai_content_agent_submit_feedback', { id: ideaId, value: value }, button, button)
                .then(() => {
                    feedbackButtons.forEach(btn => {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                    });
                });
            }
        });
    }

    if (validateLicenseButton) {
        validateLicenseButton.addEventListener('click', () => {
            const licenseKeyInput = document.getElementById('aca_ai_content_agent_license_key');
            const licenseKey = licenseKeyInput.value;

            handleApiRequest('aca_ai_content_agent_validate_license', { license_key: licenseKey }, licenseStatusSpan, validateLicenseButton)
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
                handleApiRequest('aca_ai_content_agent_suggest_update', { post_id: postId }, btn, btn)
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
            handleApiRequest('aca_ai_content_agent_fetch_gsc_data', {}, gscResults, fetchGSCButton)
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

    if (generateGSCIdeasButton) {
        generateGSCIdeasButton.addEventListener('click', () => {
            handleApiRequest('aca_ai_content_agent_generate_gsc_ideas', {}, ideasStatus, generateGSCIdeasButton)
            .then(result => {
                if (result.success) {
                    ideasStatus.textContent = result.data.message;
                    ideasStatus.style.color = '#228B22';
                    if (result.data.ideas_html) {
                        ideaList.insertAdjacentHTML('afterbegin', result.data.ideas_html);
                    }
                } else {
                    ideasStatus.textContent = 'Error: ' + result.data;
                    ideasStatus.style.color = '#DC143C';
                }
            });
        });
    }
});