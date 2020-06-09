import navbar from './components/navigation.js';

const app = new Vue({
    el: "#root",
    data: {
        username: "",
        password: "",
        passwordConfirm: ""
    },
    components: {
        navbar
    },
    methods: {
        register(e) {
            e.preventDefault();

            if(this.password === this.passwordConfirm) {
                if(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/.test(this.password)) {
                    let formData = new FormData();
                    formData.append("username", this.username);
                    formData.append("password", this.password);

                    axios.post("/api/user/create")
                    .then(response => {
                        // TODO: YES
                    })
                    .catch(error => {
                        if(error.response) {
                            Swal.fire(
                                "Error!",
                                error.response.data.message,
                                "error"
                            );
                        }
                    });
                } else {
                    Swal.fire(
                        "Error!",
                        "Your password should be atleast 8 characters long a contain atleast one digit, one lower case and one upper case character",
                        "error"
                    );
                }
            } else {
                Swal.fire(
                    "Error!",
                    "Your passwords don't match.",
                    "error"
                );
            }
        }
    }
});