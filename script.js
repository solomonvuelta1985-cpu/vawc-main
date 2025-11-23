// Multi-Step Form Navigation
let currentStep = 0;
const totalSteps = 5;

// Helper function to get today's date in YYYY-MM-DD format (local timezone)
function getTodayDateString() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Tab Navigation
function switchTab(tabName, event) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Remove active class from all nav tabs
    document.querySelectorAll('.nav-tab').forEach(navTab => {
        navTab.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName).classList.add('active');

    // Add active class to clicked nav tab
    if (event && event.target) {
        event.target.classList.add('active');
    }

    // Load data for the tab
    if (tabName === 'viewTab') {
        loadAssessments();
    } else if (tabName === 'reportsTab') {
        loadReports();
    } else if (tabName === 'addTab') {
        // Reset to step 0 when switching to Add Assessment tab
        goToStep(0);
        // Set date to today
        const dateInput = document.getElementById('assessment_date');
        if (dateInput && !dateInput.value) {
            dateInput.value = getTodayDateString();
        }
        // Refresh assessment list to update disabled barangays
        if (typeof fetchUserAssessments === 'function') {
            fetchUserAssessments();
        }
    }
}

// Navigate to specific step
function goToStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.step').forEach(step => {
        step.classList.remove('active');
    });

    // Show target step
    document.getElementById('step' + stepNumber).classList.add('active');
    currentStep = stepNumber;

    // Update progress bar
    const progress = (stepNumber / totalSteps) * 100;
    document.getElementById('progressFill').style.width = progress + '%';
    document.getElementById('progressText').textContent = `Step ${stepNumber} of ${totalSteps}`;

    // Scroll to top
    window.scrollTo(0, 0);
}

// Next Step
function nextStep() {
    if (currentStep === 0) {
        // Validate step 0
        if (!validateStep0()) {
            return;
        }
        // Update barangay info displays
        updateBarangayDisplays();
    }

    if (currentStep === 4) {
        // Last step before review, update summary
        updateSummary();
    }

    if (currentStep < totalSteps) {
        goToStep(currentStep + 1);
    }
}

// Previous Step
function previousStep() {
    if (currentStep > 0) {
        goToStep(currentStep - 1);
    }
}

// Validate Step 0
function validateStep0() {
    const barangayId = document.getElementById('barangay_id').value;
    const assessmentDate = document.getElementById('assessment_date').value;

    const isValid = barangayId !== '' && assessmentDate !== '';

    // Enable/disable next button
    document.getElementById('step0Next').disabled = !isValid;

    return isValid;
}

// Update Barangay Displays
function updateBarangayDisplays() {
    const barangaySelect = document.getElementById('barangay_id');
    const barangayName = barangaySelect.options[barangaySelect.selectedIndex].text;
    const assessmentDate = document.getElementById('assessment_date').value;

    // Update all barangay name displays
    for (let i = 1; i <= 4; i++) {
        document.getElementById('barangay_name_' + i).textContent = barangayName;
        document.getElementById('assessment_date_' + i).textContent = assessmentDate;
    }
}

// Update Section Score Display
function updateSectionScore(section) {
    const score = parseFloat(document.getElementById('section' + section + '_score').value) || 0;
    document.getElementById('score' + section + '_display').textContent = score.toFixed(1) + '/25';
}

// Select Radio Button
function selectRadio(name, value) {
    // Check the radio button
    const radioId = name + '_' + value;
    document.getElementById(radioId).checked = true;

    // Update visual selection
    document.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
        const parentOption = radio.closest('.radio-option');
        if (parentOption) {
            if (radio.checked) {
                parentOption.classList.add('selected');
            } else {
                parentOption.classList.remove('selected');
            }
        }
    });

    // Recalculate scores
    calculateScores();
}

// Toggle Checkbox
function toggleCheckbox(id) {
    const checkbox = document.getElementById(id);
    checkbox.checked = !checkbox.checked;

    // Update visual state
    const parentOption = checkbox.closest('.checkbox-option');
    if (parentOption) {
        if (checkbox.checked) {
            parentOption.classList.add('checked');
        } else {
            parentOption.classList.remove('checked');
        }
    }

    // Recalculate scores
    calculateScores();
}

// Calculate All Section Scores
function calculateScores() {
    let section1 = 0, section2 = 0, section3 = 0, section4 = 0;

    // SECTION 1: Establishment (20 points max - percentages are % of 100 total)
    // 1.a. Establishment through (5%)
    const q1a = document.querySelector('input[name="q1a"]:checked');
    if (q1a && q1a.value !== 'none') section1 += 5;

    // 1.b. Training attended (5% total)
    if (document.getElementById('q1b_antivaw')?.checked) section1 += 2;
    if (document.getElementById('q1b_gender')?.checked) section1 += 2;
    if (document.getElementById('q1b_crisis')?.checked) section1 += 1;

    // 1.c. Location (5%)
    if (document.getElementById('q1c_location')?.checked) section1 += 5;

    // 1.d. Interview room (5%)
    if (document.getElementById('q1d_room')?.checked) section1 += 5;

    // SECTION 2: Resources (20 points max - percentages are % of 100 total)
    // 2.a. Furniture (4% total)
    if (document.getElementById('q2a_table')?.checked) section2 += 1;
    if (document.getElementById('q2a_cabinet')?.checked) section2 += 1;
    if (document.getElementById('q2a_bed')?.checked) section2 += 1;
    if (document.getElementById('q2a_vehicle')?.checked) section2 += 1;

    // 2.b. Equipment (6% total)
    if (document.getElementById('q2b_phone')?.checked) section2 += 2;
    if (document.getElementById('q2b_computer')?.checked) section2 += 1;
    if (document.getElementById('q2b_camera')?.checked) section2 += 1;
    if (document.getElementById('q2b_recorder')?.checked) section2 += 1;
    if (document.getElementById('q2b_firstaid')?.checked) section2 += 1;

    // 2.c. Monitoring Tools (5% total)
    if (document.getElementById('q2c_logbook1')?.checked) section2 += 1.5;
    if (document.getElementById('q2c_logbook2')?.checked) section2 += 1.5;
    if (document.getElementById('q2c_intake')?.checked) section2 += 0.5;
    if (document.getElementById('q2c_referral')?.checked) section2 += 0.5;
    if (document.getElementById('q2c_feedback')?.checked) section2 += 0.5;
    if (document.getElementById('q2c_bpo')?.checked) section2 += 0.5;

    // 2.d. References (5% total)
    if (document.getElementById('q2d_directory')?.checked) section2 += 2;
    if (document.getElementById('q2d_handbook')?.checked) section2 += 1;
    if (document.getElementById('q2d_books')?.checked) section2 += 1;
    if (document.getElementById('q2d_bpo_flow')?.checked) section2 += 0.5;
    if (document.getElementById('q2d_vaw_flow')?.checked) section2 += 0.5;

    // SECTION 3: Policies, Plans, and Budget (20 points max - percentages are % of 100 total)
    // 3.a. AIP (5%)
    if (document.getElementById('q3a_aip')?.checked) section3 += 5;

    // 3.b. Certificate (5%)
    if (document.getElementById('q3b_cert')?.checked) section3 += 5;

    // 3.c. GAD Plan (5%)
    if (document.getElementById('q3c_gad')?.checked) section3 += 5;

    // 3.d. BDP (5%)
    if (document.getElementById('q3d_bdp')?.checked) section3 += 5;

    // SECTION 4: Accomplishments (40 points max - percentages are % of 100 total)
    // 4.a. Annual report (10%)
    if (document.getElementById('q4a_annual')?.checked) section4 += 10;

    // 4.b. Quarterly reports (10%)
    const q4b = document.querySelector('input[name="q4b"]:checked');
    if (q4b) {
        const val = q4b.value;
        if (val === '4') section4 += 10;
        else if (val === '3') section4 += 7.5;
        else if (val === '2') section4 += 5;
        else if (val === '1') section4 += 2.5;
    }

    // 4.c. Database (10%)
    if (document.getElementById('q4c_database')?.checked) section4 += 10;

    // 4.d. SOBA (10%)
    const q4d = document.querySelector('input[name="q4d"]:checked');
    if (q4d) {
        const val = q4d.value;
        if (val === '2') section4 += 10;
        else if (val === '1') section4 += 5;
    }

    // Update hidden inputs
    document.getElementById('section1_score').value = section1.toFixed(2);
    document.getElementById('section2_score').value = section2.toFixed(2);
    document.getElementById('section3_score').value = section3.toFixed(2);
    document.getElementById('section4_score').value = section4.toFixed(2);

    // Update displays - show actual points (not scaled)
    document.getElementById('score1_display').textContent = section1.toFixed(1) + '/20';
    document.getElementById('score2_display').textContent = section2.toFixed(1) + '/20';
    document.getElementById('score3_display').textContent = section3.toFixed(1) + '/20';
    document.getElementById('score4_display').textContent = section4.toFixed(1) + '/40';
}

// Update Summary
function updateSummary() {
    const barangaySelect = document.getElementById('barangay_id');
    const barangayName = barangaySelect.options[barangaySelect.selectedIndex].text;
    const assessmentDate = document.getElementById('assessment_date').value;
    const raterName = document.querySelector('.readonly-field').textContent.trim();

    document.getElementById('summary_barangay').textContent = barangayName;
    document.getElementById('summary_date').textContent = assessmentDate;
    document.getElementById('summary_rater').textContent = raterName;

    // Update section scores
    const section1 = parseFloat(document.getElementById('section1_score').value) || 0;
    const section2 = parseFloat(document.getElementById('section2_score').value) || 0;
    const section3 = parseFloat(document.getElementById('section3_score').value) || 0;
    const section4 = parseFloat(document.getElementById('section4_score').value) || 0;

    document.getElementById('summary_score1').textContent = section1.toFixed(1) + '/20';
    document.getElementById('summary_score2').textContent = section2.toFixed(1) + '/20';
    document.getElementById('summary_score3').textContent = section3.toFixed(1) + '/20';
    document.getElementById('summary_score4').textContent = section4.toFixed(1) + '/40';

    const totalScore = section1 + section2 + section3 + section4;
    document.getElementById('total_score_display').textContent = totalScore.toFixed(1) + '/100';
}

// Submit Assessment
function submitAssessment() {
    const formData = new FormData();
    formData.append('rater_id', document.getElementById('rater_id').value);
    formData.append('barangay_id', document.getElementById('barangay_id').value);
    formData.append('assessment_date', document.getElementById('assessment_date').value);
    formData.append('section1_score', document.getElementById('section1_score').value);
    formData.append('section2_score', document.getElementById('section2_score').value);
    formData.append('section3_score', document.getElementById('section3_score').value);
    formData.append('section4_score', document.getElementById('section4_score').value);
    formData.append('status', document.getElementById('status').value);
    formData.append('remarks', document.getElementById('remarks').value);

    // Validate scores
    const section1 = parseFloat(formData.get('section1_score'));
    const section2 = parseFloat(formData.get('section2_score'));
    const section3 = parseFloat(formData.get('section3_score'));
    const section4 = parseFloat(formData.get('section4_score'));

    if (section1 < 0 || section1 > 20 || section2 < 0 || section2 > 20 ||
        section3 < 0 || section3 > 20 || section4 < 0 || section4 > 40) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Score',
            text: 'Section scores must be valid: Sections 1-3 (0-20), Section 4 (0-40)',
            confirmButtonColor: '#0066CC'
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Saving Assessment...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('insert.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#28A745'
            }).then(() => {
                // Reset form
                document.getElementById('assessmentForm').reset();
                // Reset all score inputs
                for (let i = 1; i <= 4; i++) {
                    document.getElementById('section' + i + '_score').value = 0;
                    updateSectionScore(i);
                }
                // Set date to today automatically
                document.getElementById('assessment_date').value = getTodayDateString();
                // Go back to step 0
                goToStep(0);
                // Reload assessments and refresh barangay dropdown
                loadAssessments();
                // Refresh the assessment list to update disabled barangays
                if (typeof fetchUserAssessments === 'function') {
                    fetchUserAssessments();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message,
                confirmButtonColor: '#DC3545'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            text: 'Failed to connect to server: ' + error,
            confirmButtonColor: '#DC3545'
        });
    });
}

// Load Assessments
function loadAssessments() {
    const tableBody = document.getElementById('assessmentsTableBody');
    tableBody.innerHTML = '<tr><td colspan="11" class="loading"><div class="loading-spinner"></div>Loading assessments...</td></tr>';

    fetch('get_data.php?action=assessments')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="11" class="empty-state"><div class="icon">ðŸ“‹</div><h3>No Assessments Yet</h3><p>Add your first assessment using the form above</p></td></tr>';
            } else {
                tableBody.innerHTML = '';
                data.data.forEach(assessment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${assessment.id}</td>
                        <td>${assessment.rater_name}</td>
                        <td>${assessment.barangay_name}</td>
                        <td>${assessment.assessment_date}</td>
                        <td>${assessment.section1_score}</td>
                        <td>${assessment.section2_score}</td>
                        <td>${assessment.section3_score}</td>
                        <td>${assessment.section4_score}</td>
                        <td><strong>${assessment.total_score}</strong></td>
                        <td><span class="status-badge status-${assessment.status}">${assessment.status.replace('_', ' ')}</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-warning" onclick="editAssessment(${assessment.id})">Edit</button>
                                <button class="btn btn-danger" onclick="deleteAssessment(${assessment.id})">Delete</button>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        } else {
            tableBody.innerHTML = '<tr><td colspan="11" class="alert alert-danger">' + data.message + '</td></tr>';
        }
    })
    .catch(error => {
        tableBody.innerHTML = '<tr><td colspan="11" class="alert alert-danger">Failed to load assessments: ' + error + '</td></tr>';
    });
}

// Edit Assessment
function editAssessment(id) {
    fetch('get_data.php?action=assessment&id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const assessment = data.data;

            Swal.fire({
                title: 'Edit Assessment',
                html: `
                    <div style="text-align: left;">
                        <div class="form-group">
                            <label>Assessment Date</label>
                            <input type="date" id="edit_date" class="swal2-input" value="${assessment.assessment_date}" style="width: 90%;">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Section 1 Score (0-20)</label>
                                <input type="number" id="edit_section1" class="swal2-input" value="${assessment.section1_score}" min="0" max="20" step="0.1" style="width: 90%;">
                            </div>
                            <div class="form-group">
                                <label>Section 2 Score (0-20)</label>
                                <input type="number" id="edit_section2" class="swal2-input" value="${assessment.section2_score}" min="0" max="20" step="0.1" style="width: 90%;">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Section 3 Score (0-20)</label>
                                <input type="number" id="edit_section3" class="swal2-input" value="${assessment.section3_score}" min="0" max="20" step="0.1" style="width: 90%;">
                            </div>
                            <div class="form-group">
                                <label>Section 4 Score (0-40)</label>
                                <input type="number" id="edit_section4" class="swal2-input" value="${assessment.section4_score}" min="0" max="40" step="0.1" style="width: 90%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select id="edit_status" class="swal2-input" style="width: 90%;">
                                <option value="pending" ${assessment.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="in_progress" ${assessment.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                <option value="completed" ${assessment.status === 'completed' ? 'selected' : ''}>Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea id="edit_remarks" class="swal2-textarea" style="width: 90%;">${assessment.remarks || ''}</textarea>
                        </div>
                    </div>
                `,
                width: 600,
                showCancelButton: true,
                confirmButtonText: 'Update',
                confirmButtonColor: '#0066CC',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const section1 = parseFloat(document.getElementById('edit_section1').value);
                    const section2 = parseFloat(document.getElementById('edit_section2').value);
                    const section3 = parseFloat(document.getElementById('edit_section3').value);
                    const section4 = parseFloat(document.getElementById('edit_section4').value);

                    if (section1 < 0 || section1 > 20 || section2 < 0 || section2 > 20 ||
                        section3 < 0 || section3 > 20 || section4 < 0 || section4 > 40) {
                        Swal.showValidationMessage('Section scores must be valid: Sections 1-3 (0-20), Section 4 (0-40)');
                        return false;
                    }

                    return {
                        assessment_date: document.getElementById('edit_date').value,
                        section1_score: section1,
                        section2_score: section2,
                        section3_score: section3,
                        section4_score: section4,
                        status: document.getElementById('edit_status').value,
                        remarks: document.getElementById('edit_remarks').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateAssessment(id, result.value);
                }
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load assessment: ' + error,
            confirmButtonColor: '#DC3545'
        });
    });
}

// Update Assessment
function updateAssessment(id, data) {
    Swal.fire({
        title: 'Updating Assessment...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append('id', id);
    formData.append('assessment_date', data.assessment_date);
    formData.append('section1_score', data.section1_score);
    formData.append('section2_score', data.section2_score);
    formData.append('section3_score', data.section3_score);
    formData.append('section4_score', data.section4_score);
    formData.append('status', data.status);
    formData.append('remarks', data.remarks);

    fetch('update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: data.message,
                confirmButtonColor: '#28A745'
            }).then(() => {
                loadAssessments();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message,
                confirmButtonColor: '#DC3545'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            text: 'Failed to update: ' + error,
            confirmButtonColor: '#DC3545'
        });
    });
}

// Delete Assessment
function deleteAssessment(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This assessment will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('delete.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        confirmButtonColor: '#28A745'
                    }).then(() => {
                        loadAssessments();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#DC3545'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Failed to delete: ' + error,
                    confirmButtonColor: '#DC3545'
                });
            });
        }
    });
}

// Load Reports
function loadReports() {
    const container = document.getElementById('reportsContent');
    container.innerHTML = '<div class="loading"><div class="loading-spinner"></div>Loading reports...</div>';

    fetch('get_data.php?action=reports')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.data;

            container.innerHTML = `
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="label">Total Raters</div>
                        <div class="number">${stats.total_raters}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Total Barangays</div>
                        <div class="number">${stats.total_barangays}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Total Assessments</div>
                        <div class="number">${stats.total_assessments}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Average Score</div>
                        <div class="number">${stats.average_score}</div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Assessments by Barangay</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th>Total Assessments</th>
                                    <th>Average Score</th>
                                    <th>Avg Section 1</th>
                                    <th>Avg Section 2</th>
                                    <th>Avg Section 3</th>
                                    <th>Avg Section 4</th>
                                </tr>
                            </thead>
                            <tbody id="barangayStatsBody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Assessments by Rater</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Rater</th>
                                    <th>Total Assessments</th>
                                    <th>Average Score</th>
                                    <th>Completed</th>
                                    <th>In Progress</th>
                                    <th>Pending</th>
                                </tr>
                            </thead>
                            <tbody id="raterStatsBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            // Populate barangay stats
            const barangayBody = document.getElementById('barangayStatsBody');
            if (stats.by_barangay.length === 0) {
                barangayBody.innerHTML = '<tr><td colspan="7" class="empty-state">No data available</td></tr>';
            } else {
                stats.by_barangay.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.barangay}</td>
                        <td>${item.total}</td>
                        <td><strong>${item.avg_total}</strong></td>
                        <td>${item.avg_section1}</td>
                        <td>${item.avg_section2}</td>
                        <td>${item.avg_section3}</td>
                        <td>${item.avg_section4}</td>
                    `;
                    barangayBody.appendChild(row);
                });
            }

            // Populate rater stats
            const raterBody = document.getElementById('raterStatsBody');
            if (stats.by_rater.length === 0) {
                raterBody.innerHTML = '<tr><td colspan="6" class="empty-state">No data available</td></tr>';
            } else {
                stats.by_rater.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.rater}</td>
                        <td>${item.total}</td>
                        <td><strong>${item.avg_score}</strong></td>
                        <td>${item.completed}</td>
                        <td>${item.in_progress}</td>
                        <td>${item.pending}</td>
                    `;
                    raterBody.appendChild(row);
                });
            }
        } else {
            container.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        }
    })
    .catch(error => {
        container.innerHTML = '<div class="alert alert-danger">Failed to load reports: ' + error + '</div>';
    });
}

// Load Barangays for Dropdown
function loadDropdowns() {
    // Load Barangays only (rater is auto-filled from session)
    fetch('get_data.php?action=barangays')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('barangay_id');
            select.innerHTML = '<option value="">Select Barangay</option>';

            // Sort barangays alphabetically
            data.data.sort((a, b) => a.name.localeCompare(b.name));

            // Add all barangays as simple options
            data.data.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.id;
                option.textContent = barangay.name;
                select.appendChild(option);
            });

            console.log('Barangays loaded into dropdown, total:', data.data.length);

            // Signal that barangays are loaded
            if (typeof window.onBarangaysLoaded === 'function') {
                window.onBarangaysLoaded();
            }
        }
    })
    .catch(error => {
        console.error('Error loading barangays:', error);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDropdowns();
    loadAssessments();

    // Set default date to today
    document.getElementById('assessment_date').value = getTodayDateString();
});
