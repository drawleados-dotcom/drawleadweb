<script>
// Scroll reveal
const obs = new IntersectionObserver(e=>{
 e.forEach(x=>{if(x.isIntersecting){x.target.classList.add('in');obs.unobserve(x.target)}})
},{threshold:0.07});
document.querySelectorAll('.rv, .ind-card').forEach(el=>obs.observe(el));

// Dashboard cards — slide up one by one as user scrolls into each row
(function(){
 const grid = document.querySelector('.dash-grid');
 if(!grid) return;
 const cards = Array.from(grid.querySelectorAll('.dash-card'));

 const io = new IntersectionObserver((entries) => {
 entries.forEach(entry => {
 if(entry.isIntersecting){
 entry.target.classList.add('visible');
 }
 });
 }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

 cards.forEach(card => io.observe(card));
})();
</script>

<?php include __DIR__ . '/partials/booking-modal.php'; ?>
<script src="/assets/booking.js"></script>

<?php include __DIR__ . '/partials/whatsapp-widget.php'; ?>
<script src="/assets/whatsapp.js"></script>
</body>
</html>
