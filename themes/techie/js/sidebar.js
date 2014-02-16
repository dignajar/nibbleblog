function toggleSidebar() {
var sidebar = document.getElementById('sidebar');
if (sidebar.className != 'open')
    {
        // Show bar
        sidebar.style.boxShadow = '0px 5px 10px #333333';
        sidebar.style.transform = 'translateX(0%)';
        sidebar.style.msTransform = 'translateX(0%)';
        sidebar.style.WebkitTransform = 'translateX(0%)';
        sidebar.className = 'open';
    }
else
    {
        // Hide bar
        sidebar.style.boxShadow = 'none';
        sidebar.style.transform = 'translateX(-100%)';
        sidebar.style.msTtransform = 'translateX(-100%)';
        sidebar.style.WebkitTransform = 'translateX(-100%)';
        sidebar.className = 'closed';
    }
}
