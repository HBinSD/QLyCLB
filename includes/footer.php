        </main>

    </div>

</div>


<script>

function toggleAccountMenu() {

    const menu = document.getElementById("accountMenu");

    menu.classList.toggle("show");

}


function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");

    sidebar.classList.toggle("show");

}


// Click ra ngoài dropdown thì đóng

document.addEventListener("click", function(event) {

    const account = document.querySelector(".account");

    const menu = document.getElementById("accountMenu");

    if (!account.contains(event.target)) {

        menu.classList.remove("show");

    }

});

</script>


</body>

</html>