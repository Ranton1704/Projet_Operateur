document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            tabBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            // Hide all tab contents
            tabContents.forEach(content => content.classList.remove('active'));
            // Show selected tab content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Add animation to balance card on load
    const balanceCard = document.querySelector('.balance-card');
    if (balanceCard) {
        balanceCard.style.animation = 'fadeInUp 0.6s ease';
    }

    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.history-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s ease';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
