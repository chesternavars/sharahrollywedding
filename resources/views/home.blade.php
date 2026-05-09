<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sharah & Rolly 💍</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    background: url('/images/background.png') center/cover no-repeat;
    min-height: 100vh;
    font-family: 'Montserrat', sans-serif;
    color: white;
    overflow-x: hidden;
}

.overlay {
    position: fixed;
    inset: 0;
    background: radial-gradient(circle, rgba(0,0,0,0.4), rgba(0,0,0,0.85));
    z-index: 0;
}

.container-box {
    position: relative;
    z-index: 2;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.glass {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(18px);
    border-radius: 25px;
    padding: 35px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    text-align: center;
}

.couple-img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.6);
    box-shadow: 0 0 25px rgba(255,255,255,0.2);
}

.title {
    font-family: 'Great Vibes', cursive;
    font-size: 48px;
    margin-top: 10px;
}

.subtitle {
    font-size: 14px;
    opacity: 0.8;
    margin-bottom: 25px;
}

.form-select, .form-control {
    border-radius: 12px;
    border: none;
    padding: 12px;
}

.btn-romantic {
    background: linear-gradient(135deg, #e11d48, #fb7185);
    border: none;
    padding: 12px;
    border-radius: 12px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-romantic:hover {
    transform: scale(1.03);
}

.btn-outline-romantic {
    border: 1px solid rgba(255,255,255,0.5);
    color: white;
    border-radius: 12px;
    padding: 12px;
}

.btn-outline-romantic:hover {
    background: white;
    color: black;
}

.progress {
    border-radius: 50px;
    overflow: hidden;
}

.hearts {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
}

.hearts span {
    position: absolute;
    display: block;
    color: rgba(255,255,255,0.2);
    animation: float 10s linear infinite;
}

.floating-hearts {
    position: fixed;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
    z-index: 1;
}

.floating-hearts span {
    position: absolute;
    bottom: -50px;
    font-size: 40px;
    animation: floatUp linear infinite;
    opacity: 0.6;
}


/* different positions + speeds */
.floating-hearts span:nth-child(1) { left: 10%; animation-duration: 6s; font-size: 50px; }
.floating-hearts span:nth-child(2) { left: 25%; animation-duration: 8s; font-size: 35px; }
.floating-hearts span:nth-child(3) { left: 40%; animation-duration: 5s; font-size: 60px; }
.floating-hearts span:nth-child(4) { left: 55%; animation-duration: 7s; font-size: 45px; }
.floating-hearts span:nth-child(5) { left: 70%; animation-duration: 6.5s; font-size: 55px; }
.floating-hearts span:nth-child(6) { left: 85%; animation-duration: 9s; font-size: 40px; }
.floating-hearts span:nth-child(7) { left: 95%; animation-duration: 7.5s; font-size: 50px; }

@keyframes floatUp {
    0% {
        transform: translateY(0) scale(0.8);
        opacity: 0;
    }
    20% {
        opacity: 1;
    }
    100% {
        transform: translateY(-110vh) scale(1.2);
        opacity: 0;
    }
}

</style>
</head>

<body>

<div class="overlay"></div>


<!-- floating hearts -->
<div class="floating-hearts">
  <span>💖</span>
    <span>💗</span>
    <span>❤️</span>
    <span>💞</span>
    <span>💘</span>
    <span>💖</span>
    <span>❤️</span>
</div>

<div class="container-box">
    <div class="col-md-5 glass">

        <img src="/images/sharahrolly.jpg" class="couple-img">

        <div class="title">Sharah & Rolly</div>
        <div class="subtitle">Forever starts here 💍 Wedding Memories</div>

        <form action="/upload" method="POST" enctype="multipart/form-data">
            @csrf

            <select name="category" class="form-select mb-3" required>
                <option value="">Select moment 💕</option>
                <option>Bride laughing genuinely</option>
                <option>Groom fixing his suit/tie</option>
                <option>Parents getting emotional</option>
                <option>First dance spin</option>
                <option>A stolen kiss</option>
                <option>Group selfie with strangers</option>
            </select>

            <input type="file" name="files[]" multiple class="form-control mb-3">

            <div id="progressContainer" class="progress mb-3 d-none">
                <div id="progressBar" class="progress-bar bg-success" style="width:0%">0%</div>
            </div>

            <button id="uploadBtn" class="btn btn-romantic w-100 text-white mb-2">
                💌 Upload Love Memory
            </button>
        </form>

        <a href="/album" class="btn btn-outline-romantic w-100">
            💞 View Love Album
        </a>

    </div>
</div>



@if(session('error'))

<div id="errorModal" style="
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.7);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:99999;
">

    <div style="
        background:white;
        color:black;
        padding:25px;
        border-radius:15px;
        text-align:center;
        width:300px;
    ">

        <h4 style="color:red;">⚠️ Upload Limit</h4>

        <p>{{ session('error') }}</p>

        <button onclick="closeError()" class="btn btn-danger mt-3">
            OK
        </button>

    </div>

</div>

@endif


</body>
</html>

<script>
function closeError() {
    document.getElementById('errorModal').style.display = 'none';
}
</script>


<script>
document.querySelector("form").addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    let xhr = new XMLHttpRequest();

    let btn = document.getElementById("uploadBtn");
    let bar = document.getElementById("progressBar");
    let container = document.getElementById("progressContainer");

    // 🔥 DISABLE BUTTON + CHANGE TEXT
    btn.disabled = true;
    btn.innerText = "Uploading... 💌";
    btn.style.opacity = "0.7";

    container.classList.remove("d-none");

    xhr.open("POST", "/upload", true);

    xhr.setRequestHeader(
        "X-CSRF-TOKEN",
        document.querySelector('meta[name="csrf-token"]').getAttribute("content")
    );

    // 📊 PROGRESS BAR
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            let percent = Math.round((e.loaded / e.total) * 100);
            bar.style.width = percent + "%";
            bar.innerText = percent + "%";
        }
    };

    // 🚀 START STATE
    xhr.onloadstart = function() {
        bar.classList.add("bg-warning");
        bar.innerText = "Uploading to server...";
    };

    // ✅ DONE
    xhr.onload = function() {

        if (xhr.status === 200) {

            bar.style.width = "100%";
            bar.classList.remove("bg-warning");
            bar.classList.add("bg-success");
            bar.innerText = "Upload Complete ❤️";

            // reset form
            setTimeout(() => {
                document.querySelector("form").reset();
            }, 1000);

        } else {
            bar.classList.remove("bg-warning");
            bar.classList.add("bg-danger");
            bar.innerText = "Upload Failed ❌";
        }

        // 🔥 RE-ENABLE BUTTON AFTER DONE
        btn.disabled = false;
        btn.innerText = "💌 Upload Love Memory";
        btn.style.opacity = "1";
    };

    xhr.onerror = function() {
        bar.classList.add("bg-danger");
        bar.innerText = "Network Error ❌";

        btn.disabled = false;
        btn.innerText = "💌 Upload Love Memory";
        btn.style.opacity = "1";
    };

    xhr.send(formData);
});
</script>