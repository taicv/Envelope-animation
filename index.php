<!DOCTYPE html>
<html lang="vi">
<meta charset="utf-8" />
<head>
    <title>Vy & Tài</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
    // Get love parameter for dynamic OG image
    $loveParam = isset($_GET['love']) ? htmlspecialchars($_GET['love']) : '';
    $ogImageUrl = 'images/envelop-front/' . ($loveParam ? '?love=' . $loveParam : '');
    $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']);
    $fullOgImageUrl = rtrim($baseUrl, '/') . '/' . $ogImageUrl;
    ?>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($fullUrl); ?>">
    <meta property="og:title" content="Vy & Tài - Thiệp Mời">
    <meta property="og:description" content="Cô Dâu và chú rể đang viết thiệp, xin chờ một xíuuu">
    <meta property="og:image" content="<?php echo htmlspecialchars($fullOgImageUrl); ?>">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="414">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars($fullUrl); ?>">
    <meta property="twitter:title" content="Vy & Tài - Thiệp Mời">
    <meta property="twitter:description" content="Cô Dâu và chú rể đang viết thiệp, xin chờ một xíuuu">
    <meta property="twitter:image" content="<?php echo htmlspecialchars($fullOgImageUrl); ?>">
    <link rel="stylesheet" href="css/style.css<?php echo "?time=" . time() ?>">
    <style>
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg,#b7e3e7 0%, #83c6cc 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out;
        }
        
        #loading-screen.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .loading-content {
            text-align: center;
            padding: 20px;
        }
        
        .loading-message {
            font-family: Arial, sans-serif;
            font-size: 20px;
            color: #0d2e4d;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.5);
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e7dbdb;
            border-top: 5px solid #83c6cc;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 576px) {
            .loading-message {
                font-size: 18px;
                padding: 0 15px;
            }
        }
        
        @media (max-width: 380px) {
            .loading-message {
                font-size: 16px;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"
        integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js"></script>
    <script>
        // Image loading tracker
        var imagesLoaded = {
            letter: false,
            envelopeFront: false
        };
        
        function checkAllImagesLoaded() {
            if (imagesLoaded.letter && imagesLoaded.envelopeFront) {
                setTimeout(function() {
                    $('#loading-screen').addClass('hidden');
                    // Remove from DOM after transition
                    setTimeout(function() {
                        $('#loading-screen').remove();
                    }, 500);
                }, 300); // Small delay for smooth transition
            }
        }
        
        jQuery(window).on('load', function(){
            // Get love parameter from query string
            var urlParams = new URLSearchParams(window.location.search);
            var loveParam = urlParams.get('love');
            
            // Update image sources if love parameter exists
            if (loveParam) {
                $('#letter').attr('src', 'images/letter/?love=' + loveParam);
                $('.envelop__face--front img').attr('src', 'images/envelop-front/?love=' + loveParam);
            }
            
            // Track image loading
            var letterImg = document.getElementById('letter');
            var envelopeFrontImg = document.querySelector('.envelop__face--front img');
            
            // Check if letter image is already loaded (cached)
            if (letterImg.complete && letterImg.naturalHeight !== 0) {
                imagesLoaded.letter = true;
                checkAllImagesLoaded();
            } else {
                letterImg.addEventListener('load', function() {
                    imagesLoaded.letter = true;
                    checkAllImagesLoaded();
                });
                letterImg.addEventListener('error', function() {
                    imagesLoaded.letter = true; // Hide loading even on error
                    checkAllImagesLoaded();
                });
            }
            
            // Check if envelope front image is already loaded (cached)
            if (envelopeFrontImg.complete && envelopeFrontImg.naturalHeight !== 0) {
                imagesLoaded.envelopeFront = true;
                checkAllImagesLoaded();
            } else {
                envelopeFrontImg.addEventListener('load', function() {
                    imagesLoaded.envelopeFront = true;
                    checkAllImagesLoaded();
                });
                envelopeFrontImg.addEventListener('error', function() {
                    imagesLoaded.envelopeFront = true; // Hide loading even on error
                    checkAllImagesLoaded();
                });
            }
            
            // Video background with fallback handling
            var video = document.getElementById('bg-video');
            var videoLoaded = false;
            
            if (video) {
                // Timeout: If video doesn't load in 3 seconds, show fallback
                var fallbackTimeout = setTimeout(function() {
                    if (!videoLoaded) {
                        $('.wrapper').addClass('no-video');
                    }
                }, 3000);
                
                // Try to play the video
                var playPromise = video.play();
                
                if (playPromise !== undefined) {
                    playPromise.then(function() {
                        videoLoaded = true;
                        clearTimeout(fallbackTimeout);
                    }).catch(function(error) {
                        videoLoaded = true;
                        clearTimeout(fallbackTimeout);
                        // Try to play on user interaction
                        document.addEventListener('click', function() {
                            video.play().catch(function() {
                                $('.wrapper').addClass('no-video');
                            });
                        }, { once: true });
                    });
                }
                
                video.addEventListener('error', function() {
                    clearTimeout(fallbackTimeout);
                    $(this).hide();
                    $('.wrapper').addClass('no-video');
                }, true);
                
                video.addEventListener('loadeddata', function() {
                    videoLoaded = true;
                    clearTimeout(fallbackTimeout);
                });
            } else {
                $('.wrapper').addClass('no-video');
            }
            
            var animationPlayed = false;
            
            // Responsive animation parameters based on screen width
            var screenWidth = window.innerWidth;
            var letterXPercent, letterYPercent, letterScale, envelopeScale;
            
            if (screenWidth <= 380) {
                // Very small mobile - center the letter, make it readable
                letterXPercent = -5;
                letterYPercent = 0;
                letterScale = 1.8;
                envelopeScale = 0.55;
            } else if (screenWidth <= 576) {
                // Mobile - slightly offset, good readability
                letterXPercent = 10;
                letterYPercent = -5;
                letterScale = 1.9;
                envelopeScale = 0.6;
            } else if (screenWidth <= 768) {
                // Tablet
                letterXPercent = 50;
                letterYPercent = -8;
                letterScale = 1.8;
                envelopeScale = 0.68;
            } else {
                // Desktop
                letterXPercent = 100;
                letterYPercent = -10;
                letterScale = 1.75;
                envelopeScale = 0.75;
            }
            
            // Create timeline but don't play it
            var tl = gsap.timeline({repeat: 0, repeatDelay: 1, paused: true});
            tl.to(".envelop", {rotationY: 180, ease:"none", duration: 1, delay:1});
            tl.to(".cover", {rotationX: 180, ease:"none", duration: 1, delay:1}).set($('.cover'), {css:{zIndex:2}});
            tl.to("#letter",{yPercent:-70, ease:"none", duration:1}).set($('#letter'), {css:{zIndex:9}});
            tl.to("#letter",{rotation:0, ease:"none", duration:1});
            tl.add('start').to("#letter",{   ease:"none", duration:1},'start')
            .to(".envelop", {rotationZ: 10, scale:envelopeScale, ease:"none", duration: 1},'start')
            .to("#letter", {xPercent:letterXPercent, yPercent:letterYPercent, rotationZ: -10, scale: letterScale, ease:"none", duration: 1},'start');
            
            // Play animation on click/tap
            $('.scene').on('click', function(){
                if (!animationPlayed) {
                    tl.play();
                    animationPlayed = true;
                    $(this).css('cursor', 'default'); // Change cursor after animation starts
                }
            });
            
            // Add cursor pointer to indicate clickable
            $('.scene').css('cursor', 'pointer');
            
            // Reverse animation when clicking on letter after it's fully opened
            $('#letter').on('click', function(e){
                if (animationPlayed && tl.progress() === 1) {
                    e.stopPropagation(); // Prevent event bubbling
                    tl.reverse();
                    animationPlayed = false;
                    $('.scene').css('cursor', 'pointer'); // Restore pointer cursor
                }
            });
            
            // Add cursor pointer to letter when animation is complete
            tl.eventCallback("onComplete", function() {
                $('#letter').css('cursor', 'pointer');
            });
            
            // Remove pointer from letter when reversing
            tl.eventCallback("onReverseComplete", function() {
                $('#letter').css('cursor', 'default');
            });
        });

        function atou(b64) {
            return decodeURIComponent(escape(atob(b64)));
        }

        function utoa(data) {
            return btoa(unescape(encodeURIComponent(data)));
        }

    </script>
</head>

<body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loading-content">
            <div class="loading-message"><i>Cô Dâu và chú rể đang viết thiệp, xin chờ một xíuuu</i></div>
            <div class="loading-spinner"></div>
        </div>
    </div>
    
    <div class="wrapper">
        <!-- Video Background with fallback -->
        <video id="bg-video" autoplay loop muted playsinline poster="images/bg.jpg">
            <source src="images/bg.mp4" type="video/mp4">
            <!-- Fallback image is handled via poster attribute and CSS -->
        </video>
        
        <div class="scene">
            <div class="envelop">
                <div class="envelop__face envelop__face--back">
                    <div class="cover">

                    </div>
                    <img id="bg" src="images/envelope-back-transparent.png" alt="">
                    <img id="letter" src="images/letter.jpg" alt="">
                </div>
                <div class="envelop__face envelop__face--front">
                    <img src="images/envelop-front.jpg" alt="">
                </div>
            </div>

        </div>

    </div>



</body>

</html>