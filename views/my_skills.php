<?php
require_once __DIR__ . '/../config/skill_functions.php';
$skillFunc = new SkillFunctions();

$languageSkills = $skillFunc->getLanguageSkills();
$codingSkills = $skillFunc->getProgrammingLanguages();
$craftingSkills = $skillFunc->getCraftingSkills();
$organizationSkills = $skillFunc->getOrganizationSkills();
?>

<section id="skills" class="skills section">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>My Skills</h2>
        <p>An overview of my skills:</p>
        
    </div><!-- End Section Title -->

    <div class="container">
        <!-- Category Headers dengan Bootstrap Icons HARDCODED -->
        <div class="row border border-3 border-secondary p-3 mb-4 rounded bg-light">
            <div class="col-lg-3 text-center">
                <i class="bi bi-translate fs-1" style="color: #149ddd;"></i><br>
                <h5 class="mt-2"><strong>Languages</strong></h5>
            </div>
            <div class="col-md-3 text-center">
                <i class="bi bi-code-slash fs-1" style="color: #149ddd;"></i><br>
                <h5 class="mt-2"><strong>Coding</strong></h5>
            </div>
            <div class="col-md-3 text-center">
                <i class="bi bi-scissors fs-1" style="color: #149ddd;"></i><br>
                <h5 class="mt-2"><strong>Crafting</strong></h5>
            </div>
            <div class="col-lg-3 text-center">
                <i class="bi bi-kanban fs-1" style="color: #149ddd;"></i><br>
                <h5 class="mt-2"><strong>Organization</strong></h5>
            </div>
        </div>

        <!-- Skills Content - Data dari Database -->
        <div class="row g-4" id="skills-container">
            <!-- Languages Column -->
            <div class="col-lg-3 col-md-6" id="skills-languages-column">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-translate fs-2 me-2" style="color: #149ddd;"></i>
                            <h5 class="card-title mb-0">Languages</h5>
                        </div>
                        <ul class="list-unstyled">
                            <?php foreach($languageSkills as $skill): ?>
                            <li class="mb-2 pb-1 border-bottom">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong><?php echo htmlspecialchars($skill['skill_detail']); ?></strong>
                                <span class="badge bg-primary rounded-pill float-end">
                                    <?php echo $skillFunc->formatProficiency($skill['proficiency']); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Coding Column -->
            <div class="col-lg-3 col-md-6" id="skills-coding-column">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-code-slash fs-2 me-2" style="color: #149ddd;"></i>
                            <h5 class="card-title mb-0">Coding</h5>
                        </div>
                        <ul class="list-unstyled">
                            <?php foreach($codingSkills as $skill): ?>
                            <li class="mb-2 pb-1 border-bottom">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong><?php echo htmlspecialchars($skill['skill_detail']); ?></strong>
                                <span class="badge bg-primary rounded-pill float-end">
                                    <?php echo $skillFunc->formatProficiency($skill['proficiency']); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Crafting Column -->
            <div class="col-lg-3 col-md-6" id="skills-crafting-column">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-scissors fs-2 me-2" style="color: #149ddd;"></i>
                            <h5 class="card-title mb-0">Crafting</h5>
                        </div>
                        <ul class="list-unstyled">
                            <?php foreach($craftingSkills as $skill): ?>
                            <li class="mb-2 pb-1 border-bottom">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong><?php echo htmlspecialchars($skill['skill_detail']); ?></strong>
                                <span class="badge bg-primary text-dark rounded-pill float-end">
                                    <?php echo $skillFunc->formatProficiency($skill['proficiency']); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Organization Column -->
            <div class="col-lg-3 col-md-6" id="skills-organization-column">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-kanban fs-2 me-2" style="color: #149ddd;"></i>
                            <h5 class="card-title mb-0">Organization</h5>
                        </div>
                        <ul class="list-unstyled">
                            <?php foreach($organizationSkills as $skill): ?>
                            <li class="mb-2 pb-1 border-bottom">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong><?php echo htmlspecialchars($skill['skill_detail']); ?></strong>
                                <span class="badge bg-primary rounded-pill float-end">
                                    <?php echo $skillFunc->formatProficiency($skill['proficiency']); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<style>
.card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px !important;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(20, 157, 221, 0.1) !important;
}
.card-title {
    color: #050d18;
    font-weight: 600;
}
.badge {
    font-size: 0.8rem;
    padding: 6px 12px;
}
.border-bottom {
    border-bottom: 1px solid #f0f0f0 !important;
}
</style>