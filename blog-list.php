<?php
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 6;

$allBlogs = getBlogs();
$totalBlogs = count($allBlogs);
$totalPages = ceil($totalBlogs / $perPage);

// Ensure page is within bounds
if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;
$currentBlogs = array_slice($allBlogs, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - Metrix</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" />

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <header class="mx-header">
        <div class="mx-header__container">
            <!-- Logo -->
            <div class="mx-header__logo">
                <img src="assets/images/logo.png" alt="Tour Matrix" />
            </div>

            <!-- Navigation (used for both mobile & desktop) -->
            <ul class="mx-header__nav">
                <li><a href="index.html" class="mx-header__nav-item">Home</a></li>
                <li><a href="index.html#challenges" class="mx-header__nav-item">Challenges</a></li>
                <li><a href="index.html#features" class="mx-header__nav-item">Features</a></li>
                <li><a href="blog-list.php" class="mx-header__nav-item">Blog <span
                            class="mx-header__nav-item-info"></span></a></li>
                <li><a href="index.html#contact-us" class="mx-header__nav-item">Contact Us</a></li>
            </ul>

            <div class="mx-header__right-area-mobile">
                <!-- Login Button (Desktop Only) -->
                <a href="https://bridge.tourmatrix.in/index.php?r=user%2Flogin" target="_blank"
                    class="mx-btn mx-btn--small">Login/Sign Up</a>

                <!-- Hamburger Menu Toggle -->
                <button class="mx-header__menu-toggle">
                    <span class="mx-header__menu-toggle__bar"></span>
                    <span class="mx-header__menu-toggle__bar"></span>
                    <span class="mx-header__menu-toggle__bar"></span>
                </button>
            </div>
        </div>
    </header>

    <section class="mx-blog-banner mx-bg-gradiant-dark">
        <div class="mx-container">
            <h1 class="mx-title mx-blog-banner__title">Our Latest Insights</h1>
            <p class="mx-subtitle mx-blog-banner__subtitle">Stay updated with the latest trends in travel technology and
                tour operations.</p>
        </div>
    </section>

    <section class="mx-blog-list-section">
        <div class="mx-container">
            <div class="mx-blog-controls" style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                <label for="itemsPerPage" style="margin-right: 10px; font-weight: 500; color: #1e2339;">Show:</label>
                <select id="itemsPerPage" onchange="window.location.href='?limit='+this.value"
                    style="padding: 6px 12px; border-radius: 6px; border: 1px solid #c6cce3; background: white; color: #1e2339;">
                    <option value="3" <?php echo $perPage == 3 ? 'selected' : ''; ?>>3</option>
                    <option value="6" <?php echo $perPage == 6 ? 'selected' : ''; ?>>6</option>
                    <option value="9" <?php echo $perPage == 9 ? 'selected' : ''; ?>>9</option>
                    <option value="12" <?php echo $perPage == 12 ? 'selected' : ''; ?>>12</option>
                </select>
            </div>

            <div class="mx-blog-grid">
                <?php if (empty($currentBlogs)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                        <p>No blog posts found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($currentBlogs as $blog): ?>
                        <div class="mx-blog-card">
                            <div class="mx-blog-card__image">
                                <img src="<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" onerror="this.src='assets/images/solution--1.jpg'">
                            </div>
                            <div class="mx-blog-card__content">
                                <div class="mx-blog-card__meta">
                                    <span class="mx-blog-card__date"><?php echo formatDate($blog['date']); ?></span>
                                    <span class="mx-blog-card__category"><?php echo htmlspecialchars($blog['category']); ?></span>
                                </div>
                                <h3 class="mx-blog-card__title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                                <p class="mx-blog-card__excerpt"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                                <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="mx-blog-card__link">Read More 
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="mx-pagination">
                <a href="?page=<?php echo max(1, $page - 1); ?>&limit=<?php echo $perPage; ?>" class="mx-pagination__item mx-pagination__item--prev <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                        <a href="?page=<?php echo $i; ?>&limit=<?php echo $perPage; ?>" class="mx-pagination__item <?php echo $i == $page ? 'mx-pagination__item--active' : ''; ?>"><?php echo $i; ?></a>
                    <?php elseif (($i == $page - 2 && $i > 1) || ($i == $page + 2 && $i < $totalPages)): ?>
                        <span class="mx-pagination__dots">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <a href="?page=<?php echo min($totalPages, $page + 1); ?>&limit=<?php echo $perPage; ?>" class="mx-pagination__item mx-pagination__item--next <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="mx-footer">
        <div class="mx-container">
            <div class="mx-footer__item">
                <div class="mx-footer__infobox">
                    <img src="assets/images/mx-bridge-logo-white.png" alt="">
                    <p>Our Matrix is an affordable back-office automation tool for tour operators, built to cut errors,
                        speed up workflows, and help you focus on creating better travel experiences.</p>
                    <div class="mx-footer__social">
                        <a href="https://www.facebook.com/tourmatrix" class="mx-footer__social-item" target="_blank">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.5346 1.75586C5.68678 1.75586 1.75684 5.6858 1.75684 10.5336C1.75684 14.9149 4.96674 18.5463 9.1631 19.2048V13.0709H6.93435V10.5336H9.1631V8.59979C9.1631 6.39986 10.4735 5.18468 12.4786 5.18468C13.4389 5.18468 14.4435 5.35612 14.4435 5.35612V7.51628H13.3366C12.2462 7.51628 11.9062 8.1929 11.9062 8.8871V10.5336H14.3406L13.9515 13.0709H11.9062V19.2048C16.1025 18.5463 19.3124 14.9149 19.3124 10.5336C19.3124 5.6858 15.3824 1.75586 10.5346 1.75586Z"
                                    fill="#9FA9D4" />
                            </svg>

                        </a>
                        <a href="https://www.instagram.com/tourmatrix/" class="mx-footer__social-item" target="_blank">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12.0807 1.75586C13.0686 1.75749 13.5692 1.76272 14.0017 1.7756L14.1721 1.78117C14.3689 1.78816 14.5631 1.79694 14.7972 1.80791C15.7312 1.85107 16.3684 1.99883 16.928 2.21608C17.5065 2.43918 17.9952 2.74055 18.4831 3.22845C18.9702 3.71635 19.2717 4.20644 19.4955 4.78358C19.712 5.34243 19.8598 5.98029 19.9037 6.91438C19.9141 7.14846 19.9225 7.34263 19.9295 7.53946L19.935 7.70983C19.9478 8.14234 19.9537 8.64302 19.9555 9.6309L19.9562 10.2854C19.9563 10.3653 19.9563 10.4479 19.9563 10.533L19.9562 10.7806L19.9557 11.4352C19.954 12.423 19.9489 12.9237 19.936 13.3562L19.9303 13.5266C19.9234 13.7235 19.9146 13.9176 19.9037 14.1516C19.8605 15.0858 19.712 15.7229 19.4955 16.2824C19.2724 16.8611 18.9702 17.3497 18.4831 17.8376C17.9952 18.3248 17.5043 18.6261 16.928 18.8499C16.3684 19.0665 15.7312 19.2142 14.7972 19.2581C14.5631 19.2686 14.3689 19.2771 14.1721 19.2839L14.0017 19.2894C13.5692 19.3023 13.0686 19.3081 12.0807 19.3101L11.4262 19.3108C11.3462 19.3108 11.2637 19.3108 11.1785 19.3108H10.9309L10.2764 19.3102C9.28852 19.3086 8.78785 19.3033 8.35533 19.2904L8.18497 19.2849C7.98812 19.2779 7.79396 19.2691 7.55989 19.2581C6.62579 19.215 5.9894 19.0665 5.42909 18.8499C4.85121 18.6269 4.36185 18.3248 3.87395 17.8376C3.38606 17.3497 3.08542 16.8589 2.86159 16.2824C2.64434 15.7229 2.49731 15.0858 2.45342 14.1516C2.44299 13.9176 2.43449 13.7235 2.4276 13.5266L2.42207 13.3562C2.40923 12.9237 2.40338 12.423 2.40148 11.4352L2.40137 9.6309C2.403 8.64302 2.40822 8.14234 2.4211 7.70983L2.42667 7.53946C2.43367 7.34263 2.44245 7.14846 2.45342 6.91438C2.49657 5.97955 2.64434 5.34316 2.86159 4.78358C3.08468 4.20571 3.38606 3.71635 3.87395 3.22845C4.36185 2.74055 4.85195 2.43991 5.42909 2.21608C5.98867 1.99883 6.62506 1.8518 7.55989 1.80791C7.79396 1.79749 7.98812 1.78899 8.18497 1.7821L8.35533 1.77657C8.78785 1.76372 9.28852 1.75787 10.2764 1.75597L12.0807 1.75586ZM11.1785 6.14413C8.75332 6.14413 6.78964 8.10994 6.78964 10.533C6.78964 12.9582 8.75545 14.9219 11.1785 14.9219C13.6038 14.9219 15.5674 12.9561 15.5674 10.533C15.5674 8.10781 13.6016 6.14413 11.1785 6.14413ZM11.1785 7.89969C12.6329 7.89969 13.8119 9.07826 13.8119 10.533C13.8119 11.9874 12.6333 13.1663 11.1785 13.1663C9.72416 13.1663 8.5452 11.9878 8.5452 10.533C8.5452 9.07861 9.72372 7.89969 11.1785 7.89969ZM15.7869 4.82747C15.1818 4.82747 14.6897 5.31894 14.6897 5.92394C14.6897 6.52895 15.1811 7.02118 15.7869 7.02118C16.3918 7.02118 16.8841 6.52972 16.8841 5.92394C16.8841 5.31894 16.3911 4.82671 15.7869 4.82747Z"
                                    fill="#9FA9D4" />
                            </svg>

                        </a>
                        <a href="https://www.linkedin.com/company/itinges-technologies-pvt-ltd"
                            class="mx-footer__social-item" target="_blank">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.3828 16.098H14.0431V12.4317C14.0431 11.5575 14.0253 10.4324 12.8238 10.4324C11.604 10.4324 11.4176 11.3837 11.4176 12.3672V16.098H9.07793V8.55879H11.3255V9.5864H11.3558C11.6698 8.9939 12.4335 8.36853 13.5743 8.36853C15.945 8.36853 16.3835 9.92883 16.3835 11.9597L16.3828 16.098ZM6.4354 7.52718C5.68227 7.52718 5.07726 6.91756 5.07726 6.16772C5.07726 5.41854 5.68293 4.80958 6.4354 4.80958C7.1859 4.80958 7.7942 5.41854 7.7942 6.16772C7.7942 6.91756 7.18524 7.52718 6.4354 7.52718ZM7.60855 16.098H5.26225V8.55879H7.60855V16.098ZM17.5533 2.63379H4.08778C3.44328 2.63379 2.92188 3.14334 2.92188 3.77205V17.2956C2.92188 17.9249 3.44328 18.4338 4.08778 18.4338H17.5514C18.1952 18.4338 18.7219 17.9249 18.7219 17.2956V3.77205C18.7219 3.14334 18.1952 2.63379 17.5514 2.63379H17.5533Z"
                                    fill="#9FA9D4" />
                            </svg>

                        </a>
                        <a href="https://www.youtube.com/@tourmatrixbridge3863/videos" class="mx-footer__social-item"
                            target="_blank">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11.6813 3.51074C12.1501 3.51332 13.3231 3.52466 14.5694 3.57458L15.0113 3.59385C16.266 3.65326 17.5198 3.75475 18.1416 3.92813C18.971 4.16113 19.6229 4.84101 19.8433 5.70283C20.1941 7.07137 20.238 9.74244 20.2434 10.3888L20.2442 10.5228V10.5322C20.2442 10.5322 20.2442 10.5354 20.2442 10.5417L20.2434 10.6756C20.238 11.322 20.1941 13.9931 19.8433 15.3616C19.6199 16.2266 18.9679 16.9065 18.1416 17.1363C17.5198 17.3097 16.266 17.4111 15.0113 17.4706L14.5694 17.4898C13.3231 17.5397 12.1501 17.5511 11.6813 17.5537L11.4756 17.5544H11.4664C11.4664 17.5544 11.4634 17.5544 11.4573 17.5544L11.2517 17.5537C10.2595 17.5483 6.11079 17.5034 4.7913 17.1363C3.96188 16.9033 3.30997 16.2234 3.08961 15.3616C2.73878 13.9931 2.69493 11.322 2.68945 10.6756V10.3888C2.69493 9.74244 2.73878 7.07137 3.08961 5.70283C3.31303 4.83782 3.96493 4.15795 4.7913 3.92813C6.11079 3.56098 10.2595 3.5162 11.2517 3.51074H11.6813ZM9.7109 7.45999V13.6044L14.9776 10.5322L9.7109 7.45999Z"
                                    fill="#9FA9D4" />
                            </svg>

                        </a>
                    </div>
                </div>
            </div>
            <div class="mx-footer__item">
                <h3>Quick links</h3>
                <div class="mx-footer__links">
                    <a href="https://bridge.tourmatrix.in/index.php?r=user/about-us" class="mx-footer__links-item"
                        target="_blank">About Us</a>
                    <a href="https://bridge.tourmatrix.in/index.php?r=user/refund-policy" class="mx-footer__links-item"
                        target="_blank">Refund policy</a>
                    <a href="https://bridge.tourmatrix.in/index.php?r=user/terms-conditions"
                        class="mx-footer__links-item" target="_blank">Terms of service</a>
                    <a href="https://bridge.tourmatrix.in/index.php?r=user/privacy-policy" class="mx-footer__links-item"
                        target="_blank">Privacy policy</a>
                </div>
            </div>
            <div class="mx-footer__item">
                <h3>Approved by</h3>
                <img src="assets/images/mission-1.png" alt="">
                <img src="assets/images/mission-3.png" alt="">

                <h3 class="mx-mt-5 mx-mb-2">Powered by</h3>
                <img src="assets/images/mission-2.png" alt="">

            </div>
            <div class="mx-footer__item">
                <h3>Connect with us</h3>

                <div class="mx-accordion">
                    <div class="mx-accordion__item">
                        <input type="radio" name="mx-acc" id="acc-1" class="mx-accordion__radio" checked />
                        <label for="acc-1" class="mx-accordion__header">
                            Registered Address
                        </label>
                        <div class="mx-accordion__content">
                            <p>
                                21/188-4, Karayattu House,
                                Azhiyidathuchira P.O, Tiruvalla, Pathanamthitta, Kerala 689113
                            </p>
                        </div>
                    </div>

                    <div class="mx-accordion__item">
                        <input type="radio" name="mx-acc" id="acc-2" class="mx-accordion__radio" />
                        <label for="acc-2" class="mx-accordion__header">
                            Marketing & Sales Address
                        </label>
                        <div class="mx-accordion__content">
                            <p>
                                40/1672, Ground Floor, Kallada Arcade, Polassery lane, PJ Antony road,
                                Palarivattom Kochi, Kerala 682025
                            </p>
                        </div>
                    </div>

                    <div class="mx-accordion__item">
                        <input type="radio" name="mx-acc" id="acc-3" class="mx-accordion__radio" />
                        <label for="acc-3" class="mx-accordion__header">
                            Development Address
                        </label>
                        <div class="mx-accordion__content">
                            <p>
                                First Floor, St. Antony's Building, Near Mosque,
                                Manorama Junction Cherthala, Kerala 688 524
                            </p>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </footer>
    <section class="mx-copyright">
        <div class="mx-container">
            <p>© Copyright Tour Matrix. All Rights Reserved</p>
        </div>
    </section>

    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
</body>

</html>
