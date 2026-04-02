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
});
