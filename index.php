<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form</title>
  <link rel="stylesheet" href="styles.css">
  <script src="https://unpkg.com/imask"></script>


</head>

<body>
  <div class="container">
    <h1>Form</h1>
    <form id="myForm">
      <label for="name">Имя:</label>
      <input type="text" id="name" name="name" class="" required data-field="name" data-error-message="error-name"><br>
      <p class="error-message" id="error-name"></p>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" class="" required data-field="email" data-error-message="error-email"><br>
      <p class="error-message" id="error-email"></p>

      <label for="phone">Телефон:</label>
      <input type="tel" id="phone" name="phone" class="" required data-field="phone" data-error-message="error-phone"><br>
      <p class="error-message" id="error-phone"></p>

      <input type="hidden" name="time" id="time" value="<?php echo date("Y-m-d H:i:s"); ?>">

      <p id="response-message" class="error-message green" style="display: none;"></p>

      <button type="button" onclick="submitForm()">Отправить</button>
    </form>

  </div>

  <script>
    let formSubmitted = false;

    // Получите элемент поля номера телефона
    let phoneInput = document.getElementById('phone');

    // Инициализируйте маску
    let phoneMask = IMask(phoneInput, {
      mask: '+7(000)000-00-00'
    });

    function submitForm() {
      console.log("Форма отправлена"); // Отладочный вывод



      // Очищаем сообщения об ошибках и убираем подсветку полей перед отправкой формы
      document.querySelectorAll('.error').forEach(function(element) {
        element.classList.remove('error');
      });
      document.querySelectorAll('.error-message').forEach(function(element) {
        element.innerText = '';
      });

      if (formSubmitted) {
        return;
      }

      formSubmitted = true;

      let formData = new FormData(document.getElementById("myForm"));

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "submit.php", true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          console.log("Получен ответ от сервера"); // Отладочный вывод

          if (xhr.status === 200) {
            console.log("Данные успешно отправлены в Google Таблицу!");
            document.getElementById("myForm").reset();
            document.getElementById("response-message").innerText = "Данные успешно отправлены в Google Таблицу!";
            document.getElementById("response-message").style.display = "block"; // Показываем сообщение об успешной отправке
            // Убираем подсветку ошибок и сообщения об ошибках
            document.querySelectorAll('.error').forEach(function(element) {
              element.classList.remove('error');
            });
            // document.querySelectorAll('.error-message').forEach(function(element) {
            //   element.innerText = '';
            // });
            formSubmitted = false;
          } else if (xhr.status === 400) {

            let errors = JSON.parse(xhr.responseText); // Получаем ошибки в формате JSON
            // Показываем ошибки в форме
            for (let field in errors) {
              let errorInput = document.getElementById(field);
              errorInput.classList.add('error');
              let errorMessage = errors[field];
              let errorElement = document.getElementById(`error-${field}`);
              if (errorElement) {
                errorElement.innerText = errorMessage;
              }
            }
            formSubmitted = false;
          }
        }
      };
      try {
        xhr.send(formData);
      } catch (error) {
        console.log(xhr.readyState, xhr.status);
      }
    }
  </script>
</body>

</html>