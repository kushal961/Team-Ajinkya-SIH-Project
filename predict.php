<?php
if (isset($_POST['symptoms'])) {
    $symptoms = implode(",", $_POST['symptoms']);

    $command = escapeshellcmd("python predict.py \"$symptoms\"");
    $output = shell_exec($command);

    $result = json_decode($output, true);

    if ($result && isset($result['disease'])) {
        $prediction = "Predicted Disease: " . $result['disease'];
    } else {
        $prediction = "Error: Unable to get prediction.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Symptom Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #f0f9f7);
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            padding: 40px;
        }
        h3 {
            color: #00796b;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .form-check-label {
            font-size: 1.1rem;
        }
        .btn-primary {
            background-color: #00796b;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #004d40;
        }
        .result {
            margin-top: 20px;
            text-align: center;
            font-size: 1.4rem;
            color: #00796b;
            font-weight: bold;
            animation: fadeIn 1s ease-in-out;
        }
        .progress {
            height: 20px;
            margin-top: 20px;
            border-radius: 10px;
        }
        .carousel-inner img {
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }
        .tips-section h5 {
            text-align: center;
            color: #004d40;
            margin-top: 40px;
            font-weight: bold;
        }
        .carousel-item .carousel-text {
            text-align: center;
            padding-top: 10px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3><i class="fas fa-heartbeat"></i> AI Symptom Checker</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Select Your Symptoms:</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="symptoms[]" value="Fever" id="symptom1">
                            <label class="form-check-label" for="symptom1">Fever</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="symptoms[]" value="Cough" id="symptom2">
                            <label class="form-check-label" for="symptom2">Cough</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="symptoms[]" value="Headache" id="symptom3">
                            <label class="form-check-label" for="symptom3">Headache</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="symptoms[]" value="Sore Throat" id="symptom4">
                            <label class="form-check-label" for="symptom4">Sore Throat</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="symptoms[]" value="Fatigue" id="symptom5">
                            <label class="form-check-label" for="symptom5">Fatigue</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="symptoms[]" value="Body Pain" id="symptom6">
                            <label class="form-check-label" for="symptom6">Body Pain</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="symptoms[]" value="Nausea" id="symptom7">
                            <label class="form-check-label" for="symptom7">Nausea</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-notes-medical"></i> Check Symptoms</button>
        </form>

        <?php if (isset($prediction)): ?>
            <div class="result">
                <?php echo $prediction; ?>
            </div>
        <?php endif; ?>

        <div class="progress" id="progressBar" style="display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
        </div>

        <div class="tips-section">
            <h5><i class="fas fa-lightbulb"></i> Health Tips</h5>
            <div id="healthTipsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="water.avif" class="d-block w-100" alt="Stay Hydrated">
                        <div class="carousel-text">
                            <h5>Stay Hydrated</h5>
                            <p>Drinking enough water boosts energy levels and improves overall health. Aim for 8-10 glasses daily to keep your body and mind refreshed.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="heal.jpg" class="d-block w-100" alt="Eat Healthy">
                        <div class="carousel-text">
                            <h5>Eat Healthy</h5>
                            <p>Fuel your body with a balanced diet rich in fruits, vegetables, and whole grains. Eating healthy supports immunity, energy, and overall well-being.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="3627830.jpg" class="d-block w-100" alt="Exercise Regularly">
                        <div class="carousel-text">
                            <h5>Exercise Regularly</h5>
                            <p>Engage in regular physical activities to boost energy, strengthen muscles, and enhance mental health. Aim for at least 30 minutes of exercise daily.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#healthTipsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#healthTipsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('progressBar').style.display = 'block';
        });
    </script>
</body>
</html>

