const containerLogin = document.querySelector(".container-login");
const containerRegister = document.querySelector(".container-register");

const loginButton = document.querySelector("#login");
const registerButton = document.querySelector("#register");
const closeLoginButton = document.querySelector(".container-login .close-btn");
const closeRegisterButton = document.querySelector(
  ".container-register .close-btn2"
);

loginButton.onclick = () => {
  containerLogin.classList.toggle("active");
  containerRegister.classList.remove("active");
};

registerButton.onclick = () => {
  containerRegister.classList.toggle("active");
  containerLogin.classList.remove("active");
};

closeLoginButton.onclick = () => {
  containerLogin.classList.remove("active");
};

closeRegisterButton.onclick = () => {
  containerRegister.classList.remove("active");
};
document.querySelector(".register").addEventListener("click", function () {
  document.body.classList.add("register-active");
  document.querySelector(".container-register").classList.add("active");
});

document.querySelector(".close-btn2").addEventListener("click", function () {
  document.body.classList.remove("register-active");
  document.querySelector(".container-register").classList.remove("active");
});
document.querySelector(".login").addEventListener("click", function () {
  document.body.classList.add("login-active");
  document.querySelector(".container-login").classList.add("active");
});

document.querySelector(".close-btn").addEventListener("click", function () {
  document.body.classList.remove("login-active");
  document.querySelector(".container-login").classList.remove("active");
});
function showAlert(event) {
  event.preventDefault();
  alert("Silahkan Login Terlebih Dahulu");
}
