<?php
session_start();
require_once __DIR__ . "/Config/database.php";

$database = new Database();
$conn = $database->connect();

$userEmail = $_POST['username'] ?? '';
$userDisplay = $_POST['user_display'] ?? '';

$showRegister = false;
$modalMessage = '';
$modalType = ''; // 'success' or 'error'

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $email = trim($_POST['username'] ?? '');
    $user_name = trim($_POST['user_display'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$email || !$user_name || !$password || !$confirm) {
        $modalMessage = "Please fill in all fields.";
        $modalType = 'error';
        $showRegister = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $modalMessage = "Invalid email address.";
        $modalType = 'error';
        $showRegister = true;
    } elseif ($password !== $confirm) {
        $modalMessage = "Passwords do not match.";
        $modalType = 'error';
        $showRegister = true;
    } else {
        $role = null;
        $roleId = null;

        // Check Doctor Table
        $stmt = $conn->prepare("SELECT doc_id FROM doctor WHERE doc_email = ?");
        $stmt->execute([$email]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $role = 'doctor';
            $roleId = $row['doc_id'];
        }

        // Check Staff Table
        if (!$role) {
            $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE staff_email = ?");
            $stmt->execute([$email]);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $role = 'staff';
                $roleId = $row['staff_id'];
            }
        }

        // Check Patient Table
        if (!$role) {
            $stmt = $conn->prepare("SELECT pat_id FROM patient WHERE pat_email = ?");
            $stmt->execute([$email]);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $role = 'patient';
                $roleId = $row['pat_id'];
            }
        }

        if (!$role) {
            $modalMessage = "Email not found in any records!";
            $modalType = 'error';
            $showRegister = true;
        } else {
            // Check if username exists
            $check = $conn->prepare("SELECT user_id FROM user WHERE user_name = ?");
            $check->execute([$user_name]);
            if ($check->fetch()) {
                $modalMessage = "Username already taken.";
                $modalType = 'error';
                $showRegister = true;
            } else {
                // Insert user
                if ($role === 'doctor') {
                    $stmt = $conn->prepare("INSERT INTO user (user_name, user_password, doc_id, user_is_superadmin) VALUES (?, ?, ?, 0)");
                } elseif ($role === 'staff') {
                    $stmt = $conn->prepare("INSERT INTO user (user_name, user_password, staff_id, user_is_superadmin) VALUES (?, ?, ?, 0)");
                } else { // patient
                    $stmt = $conn->prepare("INSERT INTO user (user_name, user_password, pat_id, user_is_superadmin) VALUES (?, ?, ?, 0)");
                }

                $stmt->execute([$user_name, $password, $roleId]);
                $modalMessage = "🎉 $role account successfully registered! You can now log in.";
                $modalType = 'success';
                $showRegister = true;
                $userEmail = '';
                $userDisplay = '';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BioBridge Medical Center</title>
  <link rel="icon" type="image/png" href="Assets/BioBridge_Medical_Center_Logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .transition-section { transition: all 0.8s ease-in-out; }
    .translate-left { transform: translateX(-100%); opacity: 0; pointer-events: none; }
    .translate-center { transform: translateX(0); opacity: 1; pointer-events: auto; }
    .modal-bg { background-color: rgba(0,0,0,0.5); }
  </style>
</head>
<body class="flex min-h-screen bg-white text-gray-900 overflow-hidden">

<div id="container" class="relative flex w-full transition-section">

  <!-- Landing Page -->
  <section id="landing" class="transition-section translate-center flex w-full min-h-screen">
    <div class="hidden md:flex md:w-1/2">
      <img src="Assets/BioBridge_Medical_Center_Info.png" alt="Clinic Info" class="w-full h-full object-cover" />
    </div>
    <div class="flex flex-col justify-center items-center text-center p-8 w-full md:w-1/2 space-y-6">
      <img src="Assets/BioBridgeMedicalCenter.png" alt="BioBridge Logo" class="w-28 h-auto mb-4" />
      <h1 class="text-4xl font-bold">Welcome to BioBridge Medical Center</h1>
      <p class="text-gray-600 max-w-md">Your health, our priority. Book your appointment with ease and connect with our medical professionals.</p>
      <button onclick="showSection('patient-notice')" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg transition">Book an Appointment</button>
    </div>
  </section>

  <!-- Patient Notice -->
  <section id="patient-notice" class="absolute top-0 left-0 w-full min-h-screen flex items-center justify-center bg-gradient-to-r from-sky-100 to-white transition-section translate-left">
    <div class="bg-white shadow-xl rounded-2xl p-10 w-full max-w-lg text-center">
      <h1 class="text-3xl font-bold text-sky-700 mb-4">Before we continue...</h1>
      <p class="text-gray-600 mb-6">Please tell us if you're an <span class="font-semibold">existing patient</span> or a <span class="font-semibold">new patient</span>.</p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="goToAuth('login')" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg transition shadow-md w-full sm:w-auto">I'm an Existing Patient</button>
        <a href="Public/patient_register_link.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition shadow-md w-full sm:w-auto text-center block">I'm a New Patient</a>
      </div>
      <button onclick="showSection('landing')" class="text-sm text-gray-500 hover:underline mt-6 block mx-auto">← Back to Home</button>
    </div>
  </section>

  <!-- Auth Section -->
  <section id="auth" class="absolute top-0 left-0 w-full min-h-screen flex transition-section translate-left">
    <div class="w-full md:w-1/3 flex items-center justify-center p-8">
      <div class="w-full max-w-md space-y-6">
        <div class="flex justify-center">
          <img src="Assets/BioBridgeMedicalCenter.png" alt="Logo" class="w-40 h-auto mb-4" />
        </div>
        <div class="shadow-2xl rounded-xl bg-white p-8 w-full max-w-md mx-auto">

          <!-- LOGIN FORM -->
          <div id="login-form">
            <form method="POST" action="Public/login_register.php" class="space-y-4" autocomplete="off">
              <h2 class="text-2xl font-bold text-center">Sign in to your account</h2>
              <input type="text" name="username" placeholder="Username" required class="w-full p-2 bg-gray-100 border border-gray-300 rounded" />
              <div class="relative">
                <input id="login-password" type="password" name="password" placeholder="Password" required class="w-full p-2 bg-gray-100 border border-gray-300 rounded pr-10" />
                <button type="button" onclick="togglePassword('login-password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">👁️</button>
              </div>
              <button type="submit" name="login" class="w-full bg-sky-600 hover:bg-sky-700 text-white p-2 rounded">Login</button>
              <p class="text-center text-sm mt-2">
                 Already a patient but don’t have an online account yet?<br>
                <a href="#" onclick="showForm('register')" class="text-sky-600 hover:underline font-medium">Link your record now</a>
              </p>
            </form>
          </div>

          <!-- REGISTER FORM -->
          <div id="register-form" class="hidden">
            <form method="POST" class="space-y-4">
              <input type="hidden" name="register" value="1">
              <h2 class="text-2xl font-bold text-center">Create Your Account</h2>

              <label>Email</label>
              <input type="email" name="username" placeholder="Enter your registered email" required class="w-full p-2 bg-gray-100 border border-gray-300 rounded" value="<?= htmlspecialchars($userEmail) ?>">

              <label>Username</label>
              <input type="text" name="user_display" placeholder="Choose a username" required class="w-full p-2 bg-gray-100 border border-gray-300 rounded" value="<?= htmlspecialchars($userDisplay) ?>">

              <label>Password</label>
              <input type="password" name="password" placeholder="Password" required class="w-full p-2 bg-gray-100 border border-gray-300 rounded">

              <label>Confirm Password</label>
              <input type="password" name="confirm" placeholder="Confirm Password" required class="w-full p-2 bg-gray-100 border border-gray-300 rounded">

              <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white p-2 rounded">Create Account</button>
            </form>
            <button onclick="showForm('login')" class="text-sm text-gray-500 hover:underline block text-center mt-6">← Back</button>
          </div>

        </div>
      </div>
    </div>

    <div class="hidden md:block md:w-2/3">
      <img src="Assets/BioBridge_Medical_Center_Info.png" alt="Clinic Info" class="w-full h-full object-cover" />
    </div>
  </section>
</div>

<script>
function showSection(id) {
  ['landing', 'patient-notice', 'auth'].forEach(sec => {
    const el = document.getElementById(sec);
    if (sec === id) {
      el.classList.remove('translate-left');
      el.classList.add('translate-center');
    } else {
      el.classList.remove('translate-center');
      el.classList.add('translate-left');
    }
  });
}

function goToAuth(form = 'login') {
  showSection('auth');
  showForm(form);
}

function showForm(formName) {
  document.getElementById('login-form').classList.toggle('hidden', formName !== 'login');
  document.getElementById('register-form').classList.toggle('hidden', formName !== 'register');
}

function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  input.type = input.type === 'password' ? 'text' : 'password';
  btn.textContent = btn.textContent === '👁️' ? '🙈' : '👁️';
}

function showModal(message) {
  alert(message);
}

// Show modal if PHP sets a message
<?php if ($showRegister && $modalMessage): ?>
showSection('auth');
showForm('register');
showModal("<?= addslashes($modalMessage) ?>");
<?php endif; ?>
</script>

</body>
</html>
