/**
 * Skills AJAX Auto-Refresh
 * Mengambil data skill dari database setiap 1 detik
 */

(function() {
    "use strict";

    // Fungsi untuk mengambil data skill via AJAX
    function fetchSkillsData() {
        fetch('ajax/get_skills.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateSkillsUI(data.data);
                    console.log('Skills updated at:', data.timestamp);
                } else {
                    console.error('Failed to fetch skills:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching skills:', error);
            });
    }

    // Fungsi untuk update UI dengan data baru
    function updateSkillsUI(skillsData) {
        // Update Languages Section
        if (skillsData.languages) {
            updateSkillColumn('.col-lg-3.col-md-6:first-child', skillsData.languages, 'primary');
        }
        
        // Update Coding Section
        if (skillsData.coding) {
            updateSkillColumn('.col-lg-3.col-md-6:nth-child(2)', skillsData.coding, 'primary');
        }
        
        // Update Crafting Section
        if (skillsData.crafting) {
            updateSkillColumn('.col-lg-3.col-md-6:nth-child(3)', skillsData.crafting, 'primary');
        }
        
        // Update Organization Section
        if (skillsData.organization) {
            updateSkillColumn('.col-lg-3.col-md-6:nth-child(4)', skillsData.organization, 'primary');
        }
        
        // Update Programming Languages progress bars
        updateProgrammingLanguages(skillsData.coding);
    }

    // Fungsi untuk update satu kolom skill
    function updateSkillColumn(selector, skills, badgeColor) {
        const column = document.querySelector(selector);
        if (!column) return;
        
        const cardBody = column.querySelector('.card-body');
        if (!cardBody) return;
        
        const ul = cardBody.querySelector('ul.list-unstyled');
        if (!ul) return;
        
        // Kosongkan list
        ul.innerHTML = '';
        
        // Isi dengan data baru
        skills.forEach(skill => {
            const li = document.createElement('li');
            li.className = 'mb-2 pb-1 border-bottom';
            li.innerHTML = `
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong>${escapeHtml(skill.skill_detail)}</strong>
                <span class="badge bg-${badgeColor} rounded-pill float-end">
                    ${escapeHtml(skill.proficiency_formatted)}
                </span>
            `;
            ul.appendChild(li);
        });
    }

    // Fungsi untuk update Programming Languages section (progress bars)
    function updateProgrammingLanguages(codingSkills) {
        if (!codingSkills) return;
        
        const skillsSection = document.querySelector('#languages .skills-content');
        if (!skillsSection) return;
        
        // Mapping skill names ke class progress bar
        const skillMap = {};
        codingSkills.forEach(skill => {
            skillMap[skill.skill_detail.toLowerCase()] = skill.proficiency;
        });
        
        // Update setiap progress bar yang ada
        document.querySelectorAll('#languages .progress').forEach(progressItem => {
            const skillSpan = progressItem.querySelector('.skill span');
            if (!skillSpan) return;
            
            const skillName = skillSpan.textContent.trim();
            const proficiency = skillMap[skillName.toLowerCase()];
            
            if (proficiency) {
                const valSpan = progressItem.querySelector('.skill .val');
                const progressBar = progressItem.querySelector('.progress-bar');
                
                if (valSpan) {
                    valSpan.textContent = `${proficiency}%`;
                }
                if (progressBar) {
                    progressBar.setAttribute('aria-valuenow', proficiency);
                    progressBar.style.width = `${proficiency}%`;
                }
            }
        });
    }

    // Helper function untuk escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Set interval untuk refresh setiap 1 detik
    let refreshInterval;
    
    function startAutoRefresh() {
        // Ambil data segera setelah halaman dimuat
        fetchSkillsData();
        
        // Set interval untuk refresh setiap 1 detik
        refreshInterval = setInterval(fetchSkillsData, 1000);
        
        console.log('Skills auto-refresh started (interval: 1 second)');
    }

    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            console.log('Skills auto-refresh stopped');
        }
    }

    // Mulai auto-refresh ketika halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', startAutoRefresh);
    
    // Optional: Hentikan refresh ketika navigasi ke halaman lain
    window.addEventListener('beforeunload', stopAutoRefresh);
    
})();