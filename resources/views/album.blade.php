<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wedding Album 💍</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    background:
        linear-gradient(rgba(88,28,45,0.55), rgba(15,23,42,0.85)),
        url('/images/background.png') center/cover no-repeat fixed;

    min-height: 100vh;
    font-family: 'Montserrat', sans-serif;
    color: white;
    overflow-x: hidden;
}

/* HEARTS */
.floating-hearts {
    position: fixed;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
    z-index: 0;
}

.floating-hearts span {
    position: absolute;
    bottom: -50px;
    font-size: 45px;
    opacity: 0.5;
    animation: floatUp linear infinite;
}

.floating-hearts span:nth-child(1){left:10%;animation-duration:6s;}
.floating-hearts span:nth-child(2){left:25%;animation-duration:8s;font-size:35px;}
.floating-hearts span:nth-child(3){left:45%;animation-duration:5s;font-size:55px;}
.floating-hearts span:nth-child(4){left:65%;animation-duration:7s;}
.floating-hearts span:nth-child(5){left:85%;animation-duration:9s;}

@keyframes floatUp {
    0% {transform:translateY(0);opacity:0;}
    20% {opacity:1;}
    100% {transform:translateY(-110vh);opacity:0;}
}

/* HEADER */
.title {
    text-align:center;
    font-size:60px;
    font-family:'Great Vibes', cursive;
    margin-bottom:10px;
}

.subtitle {
    text-align:center;
    opacity:0.7;
    margin-bottom:25px;
}

/* BUTTONS */
.back-wrapper {
    text-align:center;
    margin-bottom:20px;
}

.back-btn {
    background: linear-gradient(135deg,#e11d48,#fb7185);
    padding:10px 20px;
    border-radius:30px;
    color:white;
    text-decoration:none;
    display:inline-block;
}

/* CATEGORY */
.cat-btn {
    margin:5px;
    padding:8px 14px;
    border-radius:20px;
    border:1px solid rgba(255,255,255,0.3);
    color:white;
    text-decoration:none;
    display:inline-block;
    transition:0.3s;
}

.cat-btn:hover {
    background: rgba(255,255,255,0.1);
}

.cat-btn.active {
    background:#e11d48;
    border:none;
}

/* IMAGE */
.img-box img {
    width:100%;
    height:220px;
    object-fit:cover;
    border-radius:14px;
    cursor:pointer;
    transition:0.3s;
    box-shadow:0 10px 25px rgba(0,0,0,0.4);
}


.img-box img:hover {
    transform:scale(1.06);
}

/* MODAL */
.image-modal {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.95);
    z-index:9999;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    padding:20px;
}

.image-modal img {
    max-width:95%;
    max-height:80vh;
    border-radius:12px;
}

.close-btn {
    position:absolute;
    top:20px;
    right:20px;
    font-size:40px;
    background:none;
    border:none;
    color:white;
}

.nav-btn {
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    background:rgba(255,255,255,0.15);
    border:none;
    color:white;
    font-size:30px;
    width:50px;
    height:50px;
    border-radius:50%;
}

.prev-btn { left:20px; }
.next-btn { right:20px; }

.modal-category {
    margin-top:15px;
    opacity:0.8;
}

/* RESPONSIVE */
@media(max-width:768px){
    .title {font-size:40px;}
    .img-box img {height:180px;}
}
</style>
</head>

<body>

<!-- HEARTS -->
<div class="floating-hearts">
    <span>💖</span>
    <span>❤️</span>
    <span>💗</span>
    <span>💞</span>
    <span>💘</span>
</div>

<div class="container py-4">

    <div class="title">Sharah & Rolly 💍</div>
    <div class="subtitle">Our forever memories ❤️</div>

    <div class="back-wrapper">
        <a href="/" class="back-btn">← Back Home</a>
    </div>

    {{-- CATEGORY --}}
    <div class="text-center mb-4">

        @php
            $categories = [
                'All',
                'Bride laughing genuinely',
                'Groom fixing his suit/tie',
                'Parents getting emotional',
                'First dance spin',
                'A stolen kiss',
                'Group selfie with strangers'
            ];
        @endphp

        @foreach($categories as $cat)
            <a href="/album?category={{ urlencode($cat) }}"
               class="cat-btn {{ ($category ?? 'All') == $cat ? 'active' : '' }}">
                {{ $cat }}
            </a>
        @endforeach

    </div>

    {{-- GALLERY --}}
    <div class="row g-3">

        @forelse($images as $index => $img)
            <div class="col-6 col-md-3">
                <div class="img-box" onclick="openModal({{ $index }})" style="cursor:pointer;">
                 @if(($img['type'] ?? 'image') === 'video')
   <div style="position:relative;cursor:pointer;" onclick="openModal({{ $index }})">

    <img src="{{ $img['thumbnail'] }}"
         style="width:100%;border-radius:14px;display:block;">

    <div style="
        position:absolute;
        inset:0;
        background:linear-gradient(to top, rgba(0,0,0,0.3), transparent);
        border-radius:14px;
    "></div>

    <div style="
        position:absolute;
        top:50%;
        left:50%;
        transform:translate(-50%, -50%);
        font-size:28px;
        color:white;
    ">
        ▶
    </div>

</div>
@else
    <img src="{{ $img['url'] }}" style="width:100%;border-radius:14px;">
@endif


               

                </div>

            </div>

        @empty

            <div class="text-center">
                <h4>No memories yet 💍</h4>
            </div>

        @endforelse

    </div>

</div>

{{-- MODAL --}}
<div id="imageModal" class="image-modal">

    <button class="close-btn" onclick="closeModal()">✕</button>

    <button class="nav-btn prev-btn" onclick="prevImage()">‹</button>

    <div id="modalContent"></div>

    <button class="nav-btn next-btn" onclick="nextImage()">›</button>

    <div id="modalCategory" class="modal-category"></div>

</div>

<script>

let images = @json($images);

console.log(images);
if (!images.length) {
    images = [];
}

let currentIndex = 0;

function openModal(i){
    if (!images.length) return;
    currentIndex = i;
    document.getElementById('imageModal').style.display='flex';
    update();
}

function update() {
    let item = images[currentIndex];
    let html;

    if (item.type === 'video') {

        let fileId = item.url.match(/id=([^&]+)/)[1];

        html = `
<div style="
    width:100%;
    max-width:900px;
    aspect-ratio: 16 / 9;
    margin:auto;
">

    <iframe src="https://drive.google.com/file/d/${fileId}/preview"
        style="
            width:100%;
            height:100%;
            border-radius:12px;
            border:none;
        "
        allow="autoplay">
    </iframe>

</div>
`;

    } else {

        html = `
            <img src="${item.url}"
                style="max-width:95%;max-height:80vh;border-radius:12px;">
        `;
    }

    document.getElementById('modalContent').innerHTML = html;
}

function closeModal(){
  
  let video = document.querySelector('#modalContent video');
    if(video) video.pause();

    document.getElementById('imageModal').style.display='none';
}

function nextImage(){
    currentIndex = (currentIndex+1)%images.length;
    update();
}

function prevImage(){
    currentIndex = (currentIndex-1+images.length)%images.length;
    update();
}


document.addEventListener('keydown', function(e){
    if(document.getElementById('imageModal').style.display === 'flex'){

        if(e.key === 'ArrowRight') nextImage();
        if(e.key === 'ArrowLeft') prevImage();
        if(e.key === 'Escape') closeModal();

    }
});

</script>

</body>
</html>