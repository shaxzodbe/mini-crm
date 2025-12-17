<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Widget</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #ffffff;
            --text: #1f2937;
            --input-bg: #f3f4f6;
            --error: #ef4444;
            --success: #10b981;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --primary: #6366f1;
                --primary-hover: #4f46e5;
                --bg: #111827;
                --text: #f9fafb;
                --input-bg: #374151;
            }
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .widget-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            box-sizing: border-box;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            background: linear-gradient(to right, var(--primary), #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        input,
        textarea {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid transparent;
            background: var(--input-bg);
            color: var(--text);
            font-size: 1rem;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: var(--primary-hover);
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .error-message {
            color: var(--error);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .success-message {
            display: none;
            text-align: center;
            color: var(--success);
            padding: 2rem 0;
        }

        .success-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .loader {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            width: 1.5rem;
            height: 1.5rem;
            animation: spin 1s linear infinite;
            margin: 0 auto;
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-btn {
            background: var(--input-bg);
            color: var(--text);
            padding: 0.75rem;
            text-align: center;
            border-radius: 0.5rem;
            cursor: pointer;
            display: block;
        }
    </style>
</head>

<body>
    <div class="widget-container">
        <div id="success-view" class="success-message">
            <div class="success-icon">✓</div>
            <h3>Ваша заявка принята!</h3>
            <p>Мы свяжемся с вами в ближайшее время.</p>
            <button onclick="resetForm()"
                style="margin-top: 1rem; background: transparent; color: var(--text); border: 1px solid var(--input-bg);">Отправить
                еще</button>
        </div>

        <form id="ticket-form" enctype="multipart/form-data">
            <h2>Связаться с нами</h2>

            <div class="form-group">
                <input type="text" name="name" placeholder="Ваше имя" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
                <div class="error-message" id="error-email"></div>
            </div>

            <div class="form-group">
                <input type="tel" name="phone" placeholder="Телефон (например +7...)" required pattern="\+\d{9,15}">
                <div class="error-message" id="error-phone"></div>
            </div>

            <div class="form-group">
                <input type="text" name="subject" placeholder="Тема обращения" required>
            </div>

            <div class="form-group">
                <textarea name="text" rows="4" placeholder="Текст сообщения" required></textarea>
            </div>

            <div class="form-group">
                <div class="file-input-wrapper">
                    <div class="file-input-btn">Прикрепить файлы</div>
                    <input type="file" name="files[]" multiple onchange="updateFileCount(this)">
                </div>
                <div id="file-count" style="font-size: 0.8rem; margin-top: 0.5rem; text-align: right; opacity: 0.7;">
                </div>
            </div>

            <div class="error-message" id="error-global" style="margin-bottom: 1rem; text-align: center;"></div>

            <button type="submit" id="submit-btn">
                <span class="btn-text">Отправить</span>
                <div class="loader"></div>
            </button>
        </form>
    </div>

    <script>
        const form = document.getElementById('ticket-form');
        const successView = document.getElementById('success-view');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = submitBtn.querySelector('.btn-text');
        const loader = submitBtn.querySelector('.loader');

        function updateFileCount(input) {
            const count = input.files.length;
            const text = count > 0 ? `Выбрано файлов: ${count}` : '';
            document.getElementById('file-count').textContent = text;
        }

        function resetForm() {
            form.reset();
            document.getElementById('file-count').textContent = '';
            successView.style.display = 'none';
            form.style.display = 'block';
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Clear errors
            document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

            // Loading state
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            loader.style.display = 'block';

            const formData = new FormData(form);

            try {
                const response = await fetch('/api/v1/tickets', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    form.style.display = 'none';
                    successView.style.display = 'block';
                } else {
                    if (response.status === 422) {
                        // Validation errors
                        for (const [key, messages] of Object.entries(data.errors)) {
                            const errorEl = document.getElementById(`error-${key}`) || document.getElementById('error-global');
                            if (errorEl) {
                                errorEl.textContent = messages[0];
                                errorEl.style.display = 'block';
                            }
                        }
                    } else if (response.status === 429) {
                        const errorEl = document.getElementById('error-global');
                        errorEl.textContent = data.message || 'Слишком много попыток. Попробуйте позже.';
                        errorEl.style.display = 'block';
                    } else {
                        throw new Error(data.message || 'Произошла ошибка');
                    }
                }
            } catch (error) {
                const errorEl = document.getElementById('error-global');
                errorEl.textContent = error.message;
                errorEl.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                btnText.style.display = 'block';
                loader.style.display = 'none';
            }
        });
    </script>
</body>

</html>
