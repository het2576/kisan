document.addEventListener('DOMContentLoaded', function() {
    // Update countdowns
    function updateCountdowns() {
        document.querySelectorAll('.time-left').forEach(function(element) {
            const endTime = new Date(element.dataset.end).getTime();
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                element.querySelector('.countdown').innerHTML = '<span class="text-danger">Auction Ended</span>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let countdown = '';
            if (days > 0) countdown += `${days}d `;
            if (hours > 0) countdown += `${hours}h `;
            countdown += `${minutes}m ${seconds}s`;

            element.querySelector('.countdown').innerHTML = countdown;
        });
    }

    // Update bid amounts
    function updateBids() {
        fetch('../api/get_auction_updates.php')
            .then(response => response.json())
            .then(data => {
                data.auctions.forEach(auction => {
                    const card = document.querySelector(`[data-auction-id="${auction.id}"]`);
                    if (card) {
                        card.querySelector('.amount').textContent = '₹' + auction.current_bid;
                    }
                });
            })
            .catch(error => console.error('Error:', error));
    }

    // Add animation to stats
    const stats = document.querySelectorAll('.stat-value');
    stats.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        animateValue(stat, 0, finalValue, 2000);
    });

    // Add hover effect to auction cards
    const cards = document.querySelectorAll('.auction-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Initial updates
    updateCountdowns();
    
    // Set intervals for updates
    setInterval(updateCountdowns, 1000);
    setInterval(updateBids, 5000);

    // Smooth scroll for create auction button
    document.querySelectorAll('a[href="create.php"]').forEach(button => {
        button.addEventListener('click', function(e) {
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                // Add a nice animation for mobile
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            }
        });
    });

    // Auto-hide success message
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 5000);
    }
});

// Animate number counting
function animateValue(obj, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
} 