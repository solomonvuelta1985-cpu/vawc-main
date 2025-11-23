// Admin Dashboard JavaScript

let overviewData = null;
let ratersData = null;
let barangaysData = null;
let matrixData = null;
let assessmentsData = null;
let rankingsData = null;
let scoreDistChart = null;
let sectionScoresChart = null;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadOverviewData();

    // Load data when tabs are clicked
    const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (event) {
            const target = event.target.getAttribute('data-bs-target');

            switch(target) {
                case '#raters':
                    if (!ratersData) loadRatersReport();
                    break;
                case '#barangays':
                    if (!barangaysData) loadBarangaysReport();
                    break;
                case '#rank':
                    if (!rankingsData) loadRankings();
                    break;
                case '#matrix':
                    if (!matrixData) loadCompletionMatrix();
                    break;
                case '#assessments':
                    if (!assessmentsData) loadAssessmentsDetail();
                    break;
            }
        });
    });
});

// Load overview data
function loadOverviewData() {
    fetch('admin_reports.php?action=overview')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                overviewData = data.data;
                renderOverview(overviewData);
                loadScoreDistribution();
            } else {
                showError('Failed to load overview data: ' + data.message);
            }
        })
        .catch(error => {
            showError('Error loading overview data: ' + error);
        });
}

// Render overview section
function renderOverview(data) {
    const statsHtml = `
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Raters</div>
                        <div class="stat-value">${data.total_raters}</div>
                        <div class="stat-detail">Assessment Officers</div>
                    </div>
                    <i class="bi bi-people-fill stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Barangays</div>
                        <div class="stat-value">${data.total_barangays}</div>
                        <div class="stat-detail">Baggao, Cagayan</div>
                    </div>
                    <i class="bi bi-geo-alt-fill stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Assessments</div>
                        <div class="stat-value">${data.total_assessments}</div>
                        <div class="stat-detail">of ${data.total_possible_assessments} possible</div>
                    </div>
                    <i class="bi bi-file-text-fill stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Completion Rate</div>
                        <div class="stat-value">${data.completion_rate}%</div>
                        <div class="stat-detail">${data.remaining_assessments} remaining</div>
                    </div>
                    <i class="bi bi-check-circle-fill stat-icon text-success"></i>
                </div>
            </div>
        </div>
    `;

    document.getElementById('overview-stats').innerHTML = statsHtml;

    // Render section scores chart
    renderSectionScoresChart(data.scores);

    // Render recent assessments
    renderRecentAssessments(data.recent_assessments);
}

// Render section scores chart
function renderSectionScoresChart(scores) {
    const ctx = document.getElementById('sectionScoresChart').getContext('2d');

    if (sectionScoresChart) {
        sectionScoresChart.destroy();
    }

    sectionScoresChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Section 1\nEstablishment', 'Section 2\nResources', 'Section 3\nPolicies', 'Section 4\nAccomplishments'],
            datasets: [{
                label: 'Average Score',
                data: [
                    parseFloat(scores.avg_section1 || 0).toFixed(2),
                    parseFloat(scores.avg_section2 || 0).toFixed(2),
                    parseFloat(scores.avg_section3 || 0).toFixed(2),
                    parseFloat(scores.avg_section4 || 0).toFixed(2)
                ],
                backgroundColor: '#0d6efd',
                borderColor: '#0d6efd',
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 40,
                    ticks: {
                        stepSize: 5
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const sectionIndex = context.dataIndex;
                            // Sections 1-3 are out of 20, Section 4 is out of 40
                            const maxScore = sectionIndex <= 2 ? 20 : 40;
                            return 'Average: ' + context.parsed.y.toFixed(2) + ' / ' + maxScore;
                        }
                    }
                }
            }
        }
    });
}

// Load and render score distribution
function loadScoreDistribution() {
    fetch('admin_reports.php?action=score_distribution')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderScoreDistribution(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading score distribution:', error);
        });
}

// Render score distribution chart
function renderScoreDistribution(distribution) {
    const ctx = document.getElementById('scoreDistributionChart').getContext('2d');

    if (scoreDistChart) {
        scoreDistChart.destroy();
    }

    scoreDistChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: distribution.map(d => d.range),
            datasets: [{
                data: distribution.map(d => d.count),
                backgroundColor: [
                    '#dc3545',
                    '#ffc107',
                    '#17a2b8',
                    '#198754'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' assessments';
                        }
                    }
                }
            }
        }
    });
}

// Render recent assessments
function renderRecentAssessments(assessments) {
    if (!assessments || assessments.length === 0) {
        document.getElementById('recent-assessments-container').innerHTML = `
            <div class="card">
                <div class="card-header"><i class="bi bi-clock-history me-2"></i>Recent Assessments</div>
                <div class="card-body">
                    <div class="no-data">No assessments found</div>
                </div>
            </div>
        `;
        return;
    }

    const tableHtml = `
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Recent Assessments</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Rater</th>
                                <th>Barangay</th>
                                <th>Score</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${assessments.map(a => `
                                <tr>
                                    <td>${formatDate(a.assessment_date)}</td>
                                    <td><strong>${a.rater_name}</strong></td>
                                    <td>${a.barangay_name}</td>
                                    <td>${getScoreBadge(a.total_score)}</td>
                                    <td>${getStatusBadge(a.status)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    document.getElementById('recent-assessments-container').innerHTML = tableHtml;
}

// Load raters report
function loadRatersReport() {
    fetch('admin_reports.php?action=raters_report')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ratersData = data.data;
                renderRatersReport(ratersData);
            } else {
                showError('Failed to load raters report: ' + data.message);
            }
        })
        .catch(error => {
            showError('Error loading raters report: ' + error);
        });
}

// Render raters report
function renderRatersReport(raters) {
    if (!raters || raters.length === 0) {
        document.getElementById('raters-table-container').innerHTML = '<div class="no-data">No raters found</div>';
        return;
    }

    const tableHtml = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Rater Name</th>
                        <th>Position</th>
                        <th>Total</th>
                        <th>Completed</th>
                        <th>In Progress</th>
                        <th>Pending</th>
                        <th>Avg Score</th>
                        <th>Min/Max</th>
                        <th>Sec 1</th>
                        <th>Sec 2</th>
                        <th>Sec 3</th>
                        <th>Sec 4</th>
                    </tr>
                </thead>
                <tbody>
                    ${raters.map(r => `
                        <tr>
                            <td><strong>${r.name}</strong></td>
                            <td><small>${r.position || 'N/A'}</small></td>
                            <td><span class="badge bg-primary">${r.total_assessments || 0}</span></td>
                            <td>${r.completed || 0}</td>
                            <td>${r.in_progress || 0}</td>
                            <td>${r.pending || 0}</td>
                            <td><strong>${r.avg_score ? parseFloat(r.avg_score).toFixed(1) : 'N/A'}</strong></td>
                            <td><small>${r.min_score ? parseFloat(r.min_score).toFixed(0) : '-'} / ${r.max_score ? parseFloat(r.max_score).toFixed(0) : '-'}</small></td>
                            <td>${r.avg_section1 ? parseFloat(r.avg_section1).toFixed(1) : '-'}</td>
                            <td>${r.avg_section2 ? parseFloat(r.avg_section2).toFixed(1) : '-'}</td>
                            <td>${r.avg_section3 ? parseFloat(r.avg_section3).toFixed(1) : '-'}</td>
                            <td>${r.avg_section4 ? parseFloat(r.avg_section4).toFixed(1) : '-'}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    document.getElementById('raters-table-container').innerHTML = tableHtml;
}

// Load barangays report
function loadBarangaysReport() {
    fetch('admin_reports.php?action=barangays_report')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                barangaysData = data.data;
                renderBarangaysReport(barangaysData);
            } else {
                showError('Failed to load barangays report: ' + data.message);
            }
        })
        .catch(error => {
            showError('Error loading barangays report: ' + error);
        });
}

// Render barangays report
function renderBarangaysReport(barangays) {
    if (!barangays || barangays.length === 0) {
        document.getElementById('barangays-table-container').innerHTML = '<div class="no-data">No barangays found</div>';
        return;
    }

    const tableHtml = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Barangay Name</th>
                        <th>Total</th>
                        <th>Completed</th>
                        <th>In Progress</th>
                        <th>Pending</th>
                        <th>Avg Score</th>
                        <th>Min/Max</th>
                        <th>Sec 1</th>
                        <th>Sec 2</th>
                        <th>Sec 3</th>
                        <th>Sec 4</th>
                    </tr>
                </thead>
                <tbody>
                    ${barangays.map(b => `
                        <tr>
                            <td><strong>${b.name}</strong></td>
                            <td><span class="badge bg-primary">${b.total_assessments || 0}</span></td>
                            <td>${b.completed || 0}</td>
                            <td>${b.in_progress || 0}</td>
                            <td>${b.pending || 0}</td>
                            <td><strong>${b.avg_score ? parseFloat(b.avg_score).toFixed(1) : 'N/A'}</strong></td>
                            <td><small>${b.min_score ? parseFloat(b.min_score).toFixed(0) : '-'} / ${b.max_score ? parseFloat(b.max_score).toFixed(0) : '-'}</small></td>
                            <td>${b.avg_section1 ? parseFloat(b.avg_section1).toFixed(1) : '-'}</td>
                            <td>${b.avg_section2 ? parseFloat(b.avg_section2).toFixed(1) : '-'}</td>
                            <td>${b.avg_section3 ? parseFloat(b.avg_section3).toFixed(1) : '-'}</td>
                            <td>${b.avg_section4 ? parseFloat(b.avg_section4).toFixed(1) : '-'}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    document.getElementById('barangays-table-container').innerHTML = tableHtml;
}

// Load rankings
function loadRankings() {
    Promise.all([
        fetch('admin_reports.php?action=barangays_report').then(r => r.json()),
        fetch('admin_reports.php?action=raters_report').then(r => r.json())
    ]).then(([barangaysData, ratersData]) => {
        if (barangaysData.success && ratersData.success) {
            rankingsData = {
                barangays: barangaysData.data,
                raters: ratersData.data
            };
            renderRankings(rankingsData);
        }
    }).catch(error => {
        showError('Error loading rankings: ' + error);
    });
}

// Render rankings
function renderRankings(data) {
    // Sort barangays by average score (descending)
    const rankedBarangays = data.barangays
        .filter(b => b.avg_score !== null)
        .sort((a, b) => parseFloat(b.avg_score) - parseFloat(a.avg_score));

    // Sort raters by average score (descending)
    const rankedRaters = data.raters
        .filter(r => r.avg_score !== null && r.name !== 'ADMINISTRATOR')
        .sort((a, b) => parseFloat(b.avg_score) - parseFloat(a.avg_score));

    // Render barangay rankings
    const barangayHtml = rankedBarangays.length > 0 ? `
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Barangay</th>
                        <th>Avg Score</th>
                        <th>Assessments</th>
                    </tr>
                </thead>
                <tbody>
                    ${rankedBarangays.map((b, index) => {
                        const rankClass = index === 0 ? 'rank-1' : index === 1 ? 'rank-2' : index === 2 ? 'rank-3' : '';
                        const rankIcon = index === 0 ? '<i class="bi bi-trophy-fill"></i>' : index === 1 ? '<i class="bi bi-award-fill"></i>' : index === 2 ? '<i class="bi bi-star-fill"></i>' : (index + 1);
                        return `
                            <tr>
                                <td>
                                    <span class="rank-badge ${rankClass}">${rankIcon}</span>
                                </td>
                                <td><strong>${b.name}</strong></td>
                                <td>${getScoreBadge(b.avg_score)}</td>
                                <td><span class="badge bg-secondary">${b.total_assessments || 0}</span></td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    ` : '<div class="no-data">No data available</div>';

    // Render rater rankings
    const raterHtml = rankedRaters.length > 0 ? `
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Rater</th>
                        <th>Avg Score</th>
                        <th>Assessments</th>
                    </tr>
                </thead>
                <tbody>
                    ${rankedRaters.map((r, index) => {
                        const rankClass = index === 0 ? 'rank-1' : index === 1 ? 'rank-2' : index === 2 ? 'rank-3' : '';
                        const rankIcon = index === 0 ? '<i class="bi bi-trophy-fill"></i>' : index === 1 ? '<i class="bi bi-award-fill"></i>' : index === 2 ? '<i class="bi bi-star-fill"></i>' : (index + 1);
                        return `
                            <tr>
                                <td>
                                    <span class="rank-badge ${rankClass}">${rankIcon}</span>
                                </td>
                                <td><strong>${r.name}</strong></td>
                                <td>${getScoreBadge(r.avg_score)}</td>
                                <td><span class="badge bg-secondary">${r.total_assessments || 0}</span></td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    ` : '<div class="no-data">No data available</div>';

    document.getElementById('barangay-rankings-container').innerHTML = barangayHtml;
    document.getElementById('rater-rankings-container').innerHTML = raterHtml;
}

// Load completion matrix
function loadCompletionMatrix() {
    fetch('admin_reports.php?action=completion_matrix')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                matrixData = data.data;
                renderCompletionMatrix(matrixData);
            } else {
                showError('Failed to load completion matrix: ' + data.message);
            }
        })
        .catch(error => {
            showError('Error loading completion matrix: ' + error);
        });
}

// Render completion matrix
function renderCompletionMatrix(data) {
    if (!data || !data.matrix || data.matrix.length === 0) {
        document.getElementById('matrix-container').innerHTML = '<div class="no-data">No data available</div>';
        return;
    }

    const tableHtml = `
        <table class="table table-bordered matrix-table">
            <thead>
                <tr>
                    <th style="position: sticky; left: 0; background: #f8f9fa; z-index: 10;">Rater</th>
                    ${data.barangays.map(b => `<th style="font-size: 0.7rem; padding: 0.5rem 0.25rem;">${b.name}</th>`).join('')}
                </tr>
            </thead>
            <tbody>
                ${data.matrix.map(rater => `
                    <tr>
                        <td style="position: sticky; left: 0; background: white; font-weight: 600; z-index: 5; border-right: 2px solid #dee2e6;">
                            ${rater.rater_name}
                        </td>
                        ${rater.barangays.map(b => {
                            const cellClass = b.status.replace('_', '-');
                            const displayText = b.status === 'not_started' ? '-' :
                                               b.status === 'completed' ? '✓' :
                                               b.status === 'in_progress' ? '◐' : '○';
                            const title = b.status === 'not_started' ? 'Not Started' :
                                         b.status === 'completed' ? `Completed - Score: ${b.score}` :
                                         b.status === 'in_progress' ? `In Progress - Score: ${b.score}` :
                                         `Pending - Score: ${b.score}`;
                            return `<td class="matrix-cell ${cellClass}" title="${title}">${displayText}</td>`;
                        }).join('')}
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

    document.getElementById('matrix-container').innerHTML = tableHtml;
}

// Load assessments detail
function loadAssessmentsDetail() {
    fetch('admin_reports.php?action=assessments_detail')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                assessmentsData = data.data;
                renderAssessmentsDetail(assessmentsData);
            } else {
                showError('Failed to load assessments: ' + data.message);
            }
        })
        .catch(error => {
            showError('Error loading assessments: ' + error);
        });
}

// Render assessments detail
function renderAssessmentsDetail(assessments) {
    if (!assessments || assessments.length === 0) {
        document.getElementById('assessments-table-container').innerHTML = '<div class="no-data">No assessments found</div>';
        return;
    }

    const tableHtml = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Rater</th>
                        <th>Position</th>
                        <th>Barangay</th>
                        <th>Sec 1</th>
                        <th>Sec 2</th>
                        <th>Sec 3</th>
                        <th>Sec 4</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    ${assessments.map(a => `
                        <tr>
                            <td><small>${formatDate(a.assessment_date)}</small></td>
                            <td><strong>${a.rater_name}</strong></td>
                            <td><small>${a.rater_position || 'N/A'}</small></td>
                            <td><strong>${a.barangay_name}</strong></td>
                            <td>${parseFloat(a.section1_score).toFixed(1)}</td>
                            <td>${parseFloat(a.section2_score).toFixed(1)}</td>
                            <td>${parseFloat(a.section3_score).toFixed(1)}</td>
                            <td>${parseFloat(a.section4_score).toFixed(1)}</td>
                            <td>${getScoreBadge(a.total_score)}</td>
                            <td>${getStatusBadge(a.status)}</td>
                            <td><small style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block;" title="${a.remarks || ''}">${a.remarks || '-'}</small></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    document.getElementById('assessments-table-container').innerHTML = tableHtml;
}

// Export functions
function exportRatersReport() {
    if (!ratersData) {
        showError('Please load the raters report first');
        return;
    }

    const csv = convertToCSV(ratersData, [
        'name', 'position', 'total_assessments', 'completed', 'in_progress', 'pending',
        'avg_score', 'min_score', 'max_score', 'avg_section1', 'avg_section2', 'avg_section3', 'avg_section4'
    ], {
        'name': 'Rater Name',
        'position': 'Position',
        'total_assessments': 'Total Assessments',
        'completed': 'Completed',
        'in_progress': 'In Progress',
        'pending': 'Pending',
        'avg_score': 'Average Score',
        'min_score': 'Min Score',
        'max_score': 'Max Score',
        'avg_section1': 'Avg Section 1',
        'avg_section2': 'Avg Section 2',
        'avg_section3': 'Avg Section 3',
        'avg_section4': 'Avg Section 4'
    });

    downloadCSV(csv, 'raters_report.csv');
}

function exportBarangaysReport() {
    if (!barangaysData) {
        showError('Please load the barangays report first');
        return;
    }

    const csv = convertToCSV(barangaysData, [
        'name', 'total_assessments', 'completed', 'in_progress', 'pending',
        'avg_score', 'min_score', 'max_score', 'avg_section1', 'avg_section2', 'avg_section3', 'avg_section4'
    ], {
        'name': 'Barangay Name',
        'total_assessments': 'Total Assessments',
        'completed': 'Completed',
        'in_progress': 'In Progress',
        'pending': 'Pending',
        'avg_score': 'Average Score',
        'min_score': 'Min Score',
        'max_score': 'Max Score',
        'avg_section1': 'Avg Section 1',
        'avg_section2': 'Avg Section 2',
        'avg_section3': 'Avg Section 3',
        'avg_section4': 'Avg Section 4'
    });

    downloadCSV(csv, 'barangays_report.csv');
}

function exportRankings() {
    if (!rankingsData) {
        showError('Please load the rankings first');
        return;
    }

    const rankedBarangays = rankingsData.barangays
        .filter(b => b.avg_score !== null)
        .sort((a, b) => parseFloat(b.avg_score) - parseFloat(a.avg_score));

    const rankedRaters = rankingsData.raters
        .filter(r => r.avg_score !== null && r.name !== 'ADMINISTRATOR')
        .sort((a, b) => parseFloat(b.avg_score) - parseFloat(a.avg_score));

    let csv = 'BARANGAY RANKINGS\n';
    csv += 'Rank,Barangay Name,Average Score,Total Assessments\n';
    rankedBarangays.forEach((b, index) => {
        csv += `${index + 1},${b.name},${parseFloat(b.avg_score).toFixed(2)},${b.total_assessments || 0}\n`;
    });

    csv += '\n\nRATER RANKINGS\n';
    csv += 'Rank,Rater Name,Average Score,Total Assessments\n';
    rankedRaters.forEach((r, index) => {
        csv += `${index + 1},${r.name},${parseFloat(r.avg_score).toFixed(2)},${r.total_assessments || 0}\n`;
    });

    downloadCSV(csv, 'rankings_report.csv');
}

function exportMatrix() {
    if (!matrixData) {
        showError('Please load the completion matrix first');
        return;
    }

    let csv = 'Rater / Barangay,' + matrixData.barangays.map(b => b.name).join(',') + '\n';

    matrixData.matrix.forEach(rater => {
        csv += rater.rater_name + ',';
        csv += rater.barangays.map(b => {
            if (b.status === 'not_started') return 'Not Started';
            return b.status.toUpperCase() + ' (' + (b.score || 0) + ')';
        }).join(',');
        csv += '\n';
    });

    downloadCSV(csv, 'completion_matrix.csv');
}

function exportAllAssessments() {
    fetch('admin_reports.php?action=export_data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const csv = convertToCSV(data.data, [
                    'rater_name', 'rater_position', 'barangay_name', 'assessment_date',
                    'section1_score', 'section2_score', 'section3_score', 'section4_score', 'total_score',
                    'status', 'remarks'
                ], {
                    'rater_name': 'Rater Name',
                    'rater_position': 'Position',
                    'barangay_name': 'Barangay',
                    'assessment_date': 'Assessment Date',
                    'section1_score': 'Section 1',
                    'section2_score': 'Section 2',
                    'section3_score': 'Section 3',
                    'section4_score': 'Section 4',
                    'total_score': 'Total Score',
                    'status': 'Status',
                    'remarks': 'Remarks'
                });

                downloadCSV(csv, 'all_assessments.csv');
            } else {
                showError('Failed to export data: ' + data.message);
            }
        })
        .catch(error => {
            showError('Error exporting data: ' + error);
        });
}

// Helper function to convert array to CSV
function convertToCSV(data, fields, headers) {
    if (!data || data.length === 0) return '';

    let csv = fields.map(f => headers[f] || f).join(',') + '\n';

    data.forEach(row => {
        csv += fields.map(field => {
            let value = row[field];
            if (value === null || value === undefined) value = '';
            if (typeof value === 'string' && value.includes(',')) {
                value = '"' + value + '"';
            }
            return value;
        }).join(',') + '\n';
    });

    return csv;
}

// Helper function to download CSV
function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    Swal.fire({
        icon: 'success',
        title: 'Export Successful',
        text: 'File has been downloaded: ' + filename,
        confirmButtonColor: '#0d6efd',
        timer: 2000
    });
}

// Print report
function printReport() {
    window.print();
}

// Logout function
function logout() {
    Swal.fire({
        title: 'Logout',
        text: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function getStatusBadge(status) {
    const badges = {
        'completed': '<span class="badge bg-success">Completed</span>',
        'in_progress': '<span class="badge bg-warning text-dark">In Progress</span>',
        'pending': '<span class="badge bg-danger">Pending</span>',
        'not_started': '<span class="badge bg-secondary">Not Started</span>'
    };
    return badges[status] || status;
}

function getScoreBadge(score) {
    if (!score) return 'N/A';

    const numScore = parseFloat(score);
    let badgeClass = '';

    if (numScore >= 76) badgeClass = 'bg-success';
    else if (numScore >= 51) badgeClass = 'bg-info text-dark';
    else if (numScore >= 26) badgeClass = 'bg-warning text-dark';
    else badgeClass = 'bg-danger';

    return `<span class="badge badge-score ${badgeClass}">${numScore.toFixed(1)}</span>`;
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#dc3545'
    });
}
