<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма Обратной Связи</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f4f8;
        }

        .feedback-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="file"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-top: 15px;
            text-align: center;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:disabled {
            background-color: #a0c9f1;
        }
    </style>
</head>
<body>
<div class="feedback-form">
    <h2>Feedback</h2>
    <div id="status-message"></div>

    <form id="ticket-form" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="Your name (required)">
            <div class="error-message" data-error="name"></div>
        </div>

        <div class="form-group">
            <label for="phone">Phone </label>
            <input type="text" name="phone" id="phone" placeholder="+998901234567" required>
            <div class="error-message" data-error="phone"></div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="example@domain.com" required>
            <div class="error-message" data-error="email"></div>
        </div>

        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" required>
            <div class="error-message" data-error="subject"></div>
        </div>

        <div class="form-group">
            <label for="text">Application text</label>
            <textarea name="text" id="text" rows="4" required></textarea>
            <div class="error-message" data-error="text"></div>
        </div>

        <div class="form-group">
            <label for="files">Files (up to 5 pcs.)</label>
            <input type="file" name="files[]" id="files" multiple>
            <div class="error-message" data-error="files"></div>
        </div>

        <button type="submit" id="submit-btn">Send application</button>
    </form>
</div>

<script>
    document.getElementById('ticket-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const statusMessage = document.getElementById('status-message');
        const submitBtn = document.getElementById('submit-btn');

        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        statusMessage.innerHTML = '';
        submitBtn.disabled = true;

        fetch('/api/tickets', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => {
                return response.json().then(data => ({status: response.status, body: data}));
            })
            .then(({status, body}) => {
                if (status === 422) {
                    statusMessage.innerHTML = '<p class="error-message">Check the entered data.</p>';
                    if (body.errors) {
                        for (const key in body.errors) {
                            const errorEl = document.querySelector(`[data-error="${key}"]`);
                            if (errorEl) {
                                errorEl.textContent = body.errors[key][0];
                            } else {
                                statusMessage.innerHTML += `<p class="error-message">${body.errors[key][0]}</p>`;
                            }
                        }
                    }
                } else if (status === 201) {
                    statusMessage.innerHTML = '<p class="success-message">🎉 Thank you! Your application has been accepted.</p>';
                    form.reset();
                } else {
                    statusMessage.innerHTML = '<p class="error-message">An unexpected error has occurred. Try again later.</p>';
                }
            })
            .catch(error => {
                console.log(error)
                statusMessage.innerHTML = '<p class="error-message">Network error. Check the connection. </p>';
            })
            .finally(() => {
                submitBtn.disabled = false;
            });
    });
</script>
</body>
</html>
