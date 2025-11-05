<!DOCTYPE html>
<html lang="vi">
<meta charset="utf-8" />
<head>
    <title>Vy & Tài</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"
        integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js"></script>
    <script>
        jQuery(window).on('load', function(){
            // Get love parameter from query string
            var urlParams = new URLSearchParams(window.location.search);
            var loveParam = urlParams.get('love');
            
            // Update image sources if love parameter exists
            if (loveParam) {
                $('#letter').attr('src', 'images/letter.php?love=' + atou(loveParam));
                $('.envelop__face--front img').attr('src', 'images/envelop-front.php?love=' + atou(loveParam));
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