<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$data->name.'-'.$data->standard}}</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Bubblegum+Sans&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS for Animations and Theme -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* bg-slate-50 */
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-40px) rotate(-6deg); }
            to { opacity: 1; transform: translateX(0) rotate(0deg); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.2); }
            50% { box-shadow: 0 0 35px rgba(99, 102, 241, 0.4); }
        }

        .animate-fadeInLeft { animation: fadeInLeft 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
        .animate-fadeInUp { animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
        .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }

        .delay-0 { animation-delay: 0ms; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }
        .delay-600 { animation-delay: 600ms; }
        .delay-700 { animation-delay: 700ms; }

        .anim-hidden { opacity: 0; will-change: transform, opacity; }

        .action-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
            padding: 1rem;
            font-weight: 700;
            color: white;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .action-link:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            filter: brightness(1.05);
        }
        .action-link:active {
            transform: translateY(-2px) scale(0.98);
            filter: brightness(0.95);
        }
    </style>
</head>
<body class="text-slate-800">


    <!-- Background Decoration -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-200/50 rounded-full filter blur-3xl opacity-50"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-200/50 rounded-full filter blur-3xl opacity-50"></div>
    </div>

                
    <header class="bg-white/80 backdrop-blur-sm sticky top-0 z-50 border-b border-slate-200/80">
      <nav class="container mx-auto px-6 py-3 flex items-center">
        <a href="javascript:history.back()" class="w-11 h-11 flex items-center justify-center rounded-full bg-sky-500 text-white shadow-md hover:bg-sky-600 transition duration-300 mr-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-purple-700 tracking-wider mb-0" style="font-family: 'Bubblegum Sans', cursive;">AI Test Paper</h1>
      </nav>
    </header>

 @yield('content')
   
    <!-- JavaScript -->
    <script>
      //  window.addEventListener('DOMContentLoaded', () => {
        //    try {
         //       const params = new URLSearchParams(window.location.search);

            //    const imageElement = document.getElementById('book-image');
           //     const titleElement = document.getElementById('book-title');
             //   const subjectElement = document.getElementById('book-subject');

         //       if (imageElement) {
           //         imageElement.alt = `Book Cover: ${title}`;
         //       }
           //     if (titleElement) {
          //          titleElement.textContent = title.toUpperCase();
        //        }
         //       if (subjectElement) {
         //           subjectElement.innerHTML = `<span class="font-bold">SUBJECT:</span> ${subject}`;
        //        }
        //    } catch (err) {
        //        alert("Something went wrong while loading the book details.");
        //    }

            // Safe error handling for all action buttons
            document.querySelectorAll('.action-link').forEach(btn => {
                btn.addEventListener('click', e => {
                    try {
                        if (btn.classList.contains('cursor-not-allowed')) {
                            e.preventDefault();
                            alert("This option is not available.");
                        }
                    } catch (err) {
                        e.preventDefault();
                        alert("Something went wrong. Please try again.");
                    }
                });
            });
        



    </script>

</body>
</html>

