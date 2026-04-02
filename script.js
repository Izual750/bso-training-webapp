document.addEventListener('DOMContentLoaded', function() {
    const stats = document.querySelectorAll('.stat');
    const cards = document.querySelectorAll('.info-card');
    
    stats.forEach((stat, index) => {
        setTimeout(() => {
            stat.style.opacity = '0';
            stat.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                stat.style.transition = 'all 0.5s ease';
                stat.style.opacity = '1';
                stat.style.transform = 'translateX(0)';
            }, 50);
        }, index * 100);
    });
    
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 80);
    });
    
    const welcome = document.querySelector('.welcome');
    if (welcome) {
        welcome.addEventListener('click', function() {
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.transform = 'translateY(-2px)';
            }, 200);
        });
    }
    
    let activeBubbles = 0;
    const maxBubbles = Math.min(window.visitCount || 1, 20);
    
    function createCatBubble(index = 0) {
        const bubble = document.createElement('div');
        bubble.className = 'cat-bubble';
        
        const img = document.createElement('img');
        img.src = 'https://cataas.com/cat?' + Date.now() + '-' + index;
        img.alt = 'Random cat';
        img.loading = 'lazy';
        
        bubble.appendChild(img);
        
        const maxX = window.innerWidth - 150;
        const maxY = window.innerHeight - 150;
        const randomX = Math.random() * maxX + 20;
        const randomY = Math.random() * maxY + 20;
        
        bubble.style.left = randomX + 'px';
        bubble.style.top = randomY + 'px';
        
        bubble.addEventListener('click', function(e) {
            bubble.classList.add('popping');
            activeBubbles--;
            
            for (let i = 0; i < 8; i++) {
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle';
                const angle = (Math.PI * 2 * i) / 8;
                const distance = 50;
                sparkle.style.left = (Math.cos(angle) * distance) + 'px';
                sparkle.style.top = (Math.sin(angle) * distance) + 'px';
                bubble.appendChild(sparkle);
                
                setTimeout(() => sparkle.remove(), 600);
            }
            
            setTimeout(() => {
                bubble.remove();
                if (activeBubbles < maxBubbles) {
                    setTimeout(() => {
                        createCatBubble(Date.now());
                        activeBubbles++;
                    }, 2000);
                }
            }, 500);
        });
        
        document.body.appendChild(bubble);
    }
    
    setTimeout(() => {
        for (let i = 0; i < maxBubbles; i++) {
            setTimeout(() => {
                createCatBubble(i);
                activeBubbles++;
            }, i * 300);
        }
    }, 1000);
    
    if (window.visitCount >= 10) {
        const walkingCat = document.createElement('div');
        walkingCat.className = 'walking-cat';
        walkingCat.textContent = '😺';
        document.body.appendChild(walkingCat);
    }
});
