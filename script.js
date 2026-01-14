const card = document.getElementById('card');
const btnSignUp = document.getElementById('btnSignUp');
const btnBack = document.getElementById('btnBack');
const signUpHeader = document.getElementById('signUpHeader');
const signUpText = document.getElementById('signUpText');

let isSignUpActive = false;

// btnSignUp.onclick = () => {
//   card.classList.add('signup-active');

//   signUpHeader.innerText = "Create Account";
//   signUpText.innerText = "Sign up to get started!";
//   btnSignUp.innerText = "Sign In";
// };

btnSignUp.addEventListener('click', () => {
  isSignUpActive = !isSignUpActive;

  card.classList.toggle('signup-active' , isSignUpActive);
  
  signUpHeader.innerText = isSignUpActive ? "Create Account" : "Welcome Back";
  signUpText.innerText = isSignUpActive ? "Sign up to get started!" : "To keep connected with us please login with your personal info";
  btnSignUp.innerText = isSignUpActive ? "Sign In" : "Sign Up";
});