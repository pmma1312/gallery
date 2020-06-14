export default {
    name: 'navbar',
    data: function() {
        return {
            activeSite: this.getActiveSite(),
            isLoggedIn: this.getIsLoggedIn(),
            sites: [
                {
                    name: "Home",
                    route: "/",
                    icon: "/public/img/icons/home.png",
                    auth: false
                },
                {
                    name: "Albums",
                    route: "/albums",
                    icon: "/public/img/icons/albums.png",
                    auth: false
                },
                {
                    name: "Panel",
                    route: "/panel",
                    icon: "/public/img/icons/panel.png",
                    auth: false
                },
                {
                    name: "Logout",
                    route: "/logout",
                    icon: "/public/img/icons/logout.png",
                    auth: true
                }
            ]
        }
    },
    methods:  {
        getActiveSite() {
            const url = new URL(window.location.href);
            return url.pathname;
        },
        getIsLoggedIn() {
            return (this.getCookie("token") != null);
        },
        getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
    },
    template: `
        <div class="navbar navbar-expand-lg navbar-dark mt-3 rounded">
            <a class="navbar-brand">pGallery</a>
            <div class="navbar">
                <ul class="navbar-nav">
                    <a v-for="site in sites" 
                       v-if="isLoggedIn || !site.auth"
                       :href="(site.route != activeSite) ? site.route : '#'"  
                       :title="site.name" class="nav item nav-link">
                        <img :src="site.icon" class="icon-navbar"
                             :class="{ active: activeSite == site.route }" >
                    </a>
                </ul>
            </div>
        </div>
    `
};