<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("Location: ../pages/homepage.php");
  exit;
}
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Login - Sistem Arsip Dokumen</title>

<style>
:root{
  --bg1:#4f63ff;
  --bg2:#ff6aa2;
  --bg3:#ffb46b;
  --ink:#ffffff;
  --muted: rgba(255,255,255,.82);
  --panel:#ffffff;
  --line:#e8e8ef;
  --purple:#6d63ff;
  --shadow: 0 26px 70px rgba(0,0,0,.28);
  --radius: 16px;
}

*{margin:0;padding:0;box-sizing:border-box;}
html, body{ height:100%; overflow:hidden; }
body{
  min-height:100vh;
  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
  background:
    radial-gradient(520px 520px at 12% 18%, rgba(166,134,240,.22) 0%, rgba(166,134,240,0) 68%),
    radial-gradient(520px 520px at 88% 82%, rgba(217,137,196,.18) 0%, rgba(217,137,196,0) 68%),
    linear-gradient(135deg, #ffffff 0%, #f6f2ff 55%, #efe7ff 100%);
  display:flex;
  align-items:center;
  justify-content:center;
  padding: 26px;
  perspective: 1400px;
}

.card{
  width:min(1100px, 100%);
  height: min(620px, 92vh);
  background: transparent;
  border-radius: var(--radius);
  box-shadow:
    0 40px 90px rgba(31,21,79,.28),
    0 10px 26px rgba(31,21,79,.16);
  overflow:hidden;
  display:grid;
  grid-template-columns: 1.15fr .85fr;
  border: 1px solid rgba(255,255,255,.18);
  animation: card-in .6s ease-out both;
  transform: rotateX(3deg) rotateY(-4deg);
  transform-style: preserve-3d;
}

/* LEFT */
.left{
  position:relative;
  padding: 70px 70px;
  color: var(--ink);
  background:
    radial-gradient(220px 220px at 62% 18%, rgba(255,255,255,.16) 0%, rgba(255,255,255,0) 65%),
    linear-gradient(180deg, #4f63ff 0%, #8a64ff 48%, #ff6aa2 100%);
  animation: panel-in .7s ease-out .1s both;
}

.left h1{
  font-size: 56px;
  line-height: 1.02;
  letter-spacing: .2px;
  margin-bottom: 16px;
  font-weight: 800;
}

.left p{
  width:min(520px, 100%);
  font-size: 18px;
  line-height: 1.45;
  color: var(--muted);
}

/* decorative shapes bottom-left */
.deco{
  position:absolute;
  left:-40px;
  bottom:-50px;
  width: 560px;
  height: 320px;
  pointer-events:none;
  opacity: .95;
  filter: drop-shadow(0 22px 40px rgba(0,0,0,.18));
}
.deco .pill{
  position:absolute;
  border-radius: 999px;
  background: linear-gradient(135deg, #ff6aa2, #ffb46b);
}
.deco .pill.p1{ width: 190px; height: 72px; left: 40px; bottom: 78px; transform: rotate(-18deg); opacity:.88; }
.deco .pill.p2{ width: 150px; height: 58px; left: 240px; bottom: 34px; transform: rotate(18deg); opacity:.65; }
.deco .pill.p3{ width: 210px; height: 74px; left: 360px; bottom: 92px; transform: rotate(-12deg); opacity:.55; }

.deco .streak{
  position:absolute;
  height: 6px;
  border-radius: 99px;
  background: rgba(255, 205, 140, .85);
  transform: rotate(-25deg);
}
.deco .s1{ width: 160px; left: 76px; bottom: 190px; opacity:.55; }
.deco .s2{ width: 220px; left: 210px; bottom: 140px; opacity:.35; }
.deco .s3{ width: 140px; left: 390px; bottom: 150px; opacity:.35; }
.deco .s4{ width: 220px; left: 120px; bottom: 60px;  opacity:.28; }
.deco .s5{ width: 180px; left: 330px; bottom: 35px;  opacity:.22; }

/* RIGHT PANEL */
.right{
  position:relative;
  background:
    linear-gradient(180deg, rgba(255,255,255,.78) 0%, rgba(185, 167, 236, 0.68) 100%);
  backdrop-filter: blur(22px) saturate(135%);
  -webkit-backdrop-filter: blur(22px) saturate(135%);
  border-left: 1px solid rgba(255,255,255,.55);
  border-right: 1px solid rgba(255,255,255,.25);
  border-top: 1px solid rgba(255,255,255,.35);
  border-bottom: 1px solid rgba(165,140,230,.35);
  box-shadow:
    0 30px 80px rgba(43,30,99,.22),
    0 8px 24px rgba(43,30,99,.12),
    inset 0 1px 0 rgba(255,255,255,.7);
  transform: perspective(1200px) rotateY(-4deg) translateZ(0);
  transform-origin: right center;
  padding: 90px 90px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  animation: panel-in .7s ease-out .2s both;
  overflow:hidden;
}
.right::before{
  content:"";
  position:absolute;
  inset:0;
  background:
    radial-gradient(300px 220px at 20% 10%, rgba(255,255,255,.45) 0%, rgba(255,255,255,0) 70%),
    linear-gradient(135deg, rgba(255,255,255,.2), rgba(255,255,255,0) 55%);
  pointer-events:none;
}

.title{
  text-align:center;
  font-weight: 800;
  letter-spacing: 2.2px;
  color: #7f67ff;
  margin-bottom: 38px;
}

.alert-error{
  background:#ffe8e8;
  border: 1px solid #ffd0d0;
  color:#b00020;
  padding: 12px 14px;
  border-radius: 12px;
  font-size: 13px;
  margin: 0 auto 18px;
  width: min(360px, 100%);
  display:flex;
  gap: 8px;
  align-items:flex-start;
}

.field{
  width: min(360px, 100%);
  margin: 0 auto;
  display:flex;
  flex-direction:column;
  gap: 18px;
}

.input{
  position:relative;
}
.input input{
  width:100%;
  height: 46px;
  border-radius: 999px;
  border: 1px solid rgba(120,95,210,.45);
  background: rgba(255,255,255,.75);
  padding: 0 18px 0 46px;
  outline:none;
  font-size: 14px;
  color:#4b5563;
  transition:.2s;
  box-shadow:
    inset 0 1px 1px rgba(255,255,255,.6),
    0 6px 16px rgba(120,95,210,.12);
}
.input input:focus{
  border-color: rgba(120,95,210,.75);
  box-shadow:
    0 0 0 4px rgba(120,95,210,.18),
    0 10px 22px rgba(120,95,210,.18);
  background:#fff;
}

.icon{
  position:absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  opacity: .75;
  color:#7f67ff;
}

.row{
  display:flex;
  justify-content:space-between;
  align-items:center;
  font-size: 12px;
  color:#a1a1b0;
  margin-top: -6px;
}

.chk{
  display:flex;
  align-items:center;
  gap: 8px;
  user-select:none;
}
.chk input{ accent-color: #7f67ff; }

.row a{
  color:#a1a1b0;
  text-decoration:none;
}
.row a:hover{ text-decoration:underline; }

.btn{
  width: min(190px, 100%);
  height: 44px;
  margin: 18px auto 0;
  border:none;
  border-radius: 999px;
  cursor:pointer;
  font-weight: 800;
  color:#fff;
  letter-spacing:.6px;
  background: #7c6be2;
  box-shadow: 0 16px 30px rgba(124,107,226,.28);
  transition:.2s;
}
.btn:hover{ transform: translateY(-1px); }
.btn:active{ transform: translateY(0); }

.bottom{
  text-align:center;
  margin-top: 26px;
  font-size: 12px;
  color:#b8b8c6;
}
.bottom a{
  color:#7f67ff;
  font-weight: 800;
  text-decoration:none;
}
.bottom a:hover{ text-decoration:underline; }

@keyframes card-in{
  from{ opacity:0; transform: translateY(18px) scale(.98); }
  to{ opacity:1; transform: translateY(0) scale(1); }
}
@keyframes panel-in{
  from{ opacity:0; transform: translateY(12px); }
  to{ opacity:1; transform: translateY(0); }
}

/* RESPONSIVE */
@media (max-width: 980px){
  .card{ grid-template-columns: 1fr; height:auto; }
  .left{ padding: 52px 34px; min-height: 340px; }
  .left h1{ font-size: 44px; }
  .right{ padding: 52px 26px; }
  .deco{ left:-80px; bottom:-80px; transform: scale(.85); }
}
@media (max-width: 520px){
  .left h1{ font-size: 38px; }
  .left p{ font-size: 15px; }
  .right{ padding: 44px 18px; }
}
</style>
</head>

<body>
  <div class="card">
    <!-- LEFT SIDE -->
    <section class="left">
      <h1>Sistem Arsip & Pemusnahan Dokumen</h1>
      <p>
        Platform digital untuk pengelolaan, sortir, dan pencatatan pemusnahan arsip secara tertib, aman, dan sesuai ketentuan.
      </p>

      <div class="deco" aria-hidden="true">
        <div class="pill p1"></div>
        <div class="pill p2"></div>
        <div class="pill p3"></div>
        <div class="streak s1"></div>
        <div class="streak s2"></div>
        <div class="streak s3"></div>
        <div class="streak s4"></div>
        <div class="streak s5"></div>
      </div>
    </section>

    <!-- RIGHT SIDE -->
    <section class="right">
      <div class="title">USER LOGIN</div>

      <?php if ($error): ?>
        <div class="alert-error">
          <span>❌</span>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form action="../auth/proses_login.php" method="POST">
        <div class="field">
          <div class="input">
            <span class="icon" aria-hidden="true">
              <!-- user icon -->
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="1.7"/>
                <path d="M4 20a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
              </svg>
            </span>
            <input type="text" name="username" placeholder="username" required autofocus>
          </div>

          <div class="input">
            <span class="icon" aria-hidden="true">
              <!-- lock icon -->
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M7 11h10v9H7z" stroke="currentColor" stroke-width="1.7" />
                <path d="M12 15v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
              </svg>
            </span>
            <input type="password" name="password" placeholder="password" required>
          </div>

          <button class="btn" type="submit">LOGIN</button>

        </div>
      </form>
    </section>
  </div>
</body>
</html>