<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Denegado</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            margin-top: 100px;
            background: #f0f2f5;
        }
        .card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: inline-block;
            animation: fadeIn 0.8s ease-in-out;
        }
        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #dc3545;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        .countdown {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        let seconds = 10;
        function updateCountdown() {
            document.getElementById('countdown').textContent = seconds;
            if (seconds === 0) {
                window.history.back(); // O puedes redirigir con: window.location.href = '/';
            } else {
                seconds--;
                setTimeout(updateCountdown, 1000);
            }
        }
        window.onload = updateCountdown;
    </script>
</head>
<body>
    <div class="card">
        <div class="alert">
            <h2>🚫 {{ $message }}</h2>
        </div>
        <div class="loader"></div>
        <p class="countdown">Regresando en <span id="countdown">10</span> segundos...</p>
    </div>
</body>
</html>
