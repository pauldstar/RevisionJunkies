'use strict';

$(_=> init());

let $loginForm = $('#login-form'),

    $signupForm = $('#signup-form'),
    $signupUsername = $('#signup-username'),
    $signupEmail = $('#signup-email');

function init()
{
  $loginForm.submit(validateForm);
  $signupForm.submit(validateForm);
  $signupForm.find('input').keydown(hideValidation);
  $signupForm.find('input').focusout(validateInput);
}

function validateForm(event)
{
  if (this.checkValidity() === false)
  {
    event.preventDefault();
    event.stopPropagation();
    this.classList.add('was-validated');
  }
}

function hideValidation(event)
{
  let $this = $(this);
  $this.removeClass('is-invalid');
  $this.removeClass('is-valid');
}

function validateInput()
{
  let $this = $(this),
      inputText = $this.val();

  if (!inputText) return;

  switch ($this.attr('id'))
  {
    case 'signup-username':
      isAvailable('username', inputText, $this);
      break;
    case 'signup-email':
      isAvailable('email', inputText, $this);
      if (!isEmail(inputText)) return;
      break;
    case 'signup-firstname':
    case 'signup-lastname':
      isValidName(inputText, $this);
      break;
  }
}

function isValidName(inputText, $input)
{
  let regex = /^[A-Za-z][A-Za-z]*(?:-[A-Za-z]+)*(?:'[A-Za-z]+)*$/;
  showValidationMessage(regex.test(inputText), $input)
}

function isAvailable(inputType, inputText, $input)
{
  $.ajax({
    url: `${SITE_URL}user/is_unique/${inputType}`,
    data: { inputText: inputText },
    dataType: 'JSON',
    success: data => showValidationMessage(data.response, $input)
  });
}

function showValidationMessage(isValid, $input)
{
  if (isValid)
  {
    $input.removeClass('is-invalid');
    $input.addClass('is-valid');
  }
  else
  {
    $input.addClass('is-invalid');
    $input.removeClass('is-valid');
  }
}

function isEmail(email)
{
  let regex =
    /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

  return regex.test(email);
}
