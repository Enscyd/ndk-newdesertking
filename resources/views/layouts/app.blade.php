<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NDK System</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.tailwindcss.com"></script>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<style>

/* NAV ITEM */
.nav-item{
position:relative;
padding-bottom:4px;
white-space:nowrap;
cursor:pointer;
}

/* HOVER LINE */
.nav-item::after{
content:"";
position:absolute;
left:0;
bottom:0;
height:2px;
width:0%;
background:#14b8a6;
transition:.3s;
}

.nav-item:hover::after{
width:100%;
}

/* ACTIVE MENU */
.nav-item.active{
color:#14b8a6;
font-weight:600;
}

.nav-item.active::after{
width:100%;
}

/* MOBILE MENU ANIMATION */
#mobileMenu{
transform-origin:top;
transform:scaleY(0);
opacity:0;
transition:.25s;
}

#mobileMenu.open{
transform:scaleY(1);
opacity:1;
}

/* STICKY NAVBAR */
.navbar{
position:sticky;
top:0;
z-index:1000;
}

</style>

</head>

<body class="bg-gray-100">

<!-- NAVBAR -->

<nav class="navbar bg-gray-900 text-white shadow">

<div class="w-full flex items-center h-16 px-6">

<!-- LOGO -->

<div class="text-2xl font-bold text-teal-400">
NDK System
</div>

<!-- DESKTOP MENU -->

<ul class="hidden md:flex space-x-8 ml-auto items-center">

<!-- WORKSHOP -->

<li x-data="{open:false}"
@mouseenter="open=true"
@mouseleave="open=false"
class="relative nav-item">

<button>
WorkShop Bills
</button>

<ul x-show="open"
x-transition
@mouseenter="open=true"
@mouseleave="open=false"
class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Workshop
</a>
</li>

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Suggestions
</a>
</li>

</ul>
</li>

<!-- EMPLOYEE -->

<li x-data="{open:false}"
@mouseenter="open=true"
@mouseleave="open=false"
class="relative nav-item">

<button>
Employee
</button>

<ul x-show="open"
x-transition
@mouseenter="open=true"
@mouseleave="open=false"
class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">

<li>
<a href="{{ route('employee.index') }}" class="block px-4 py-2 hover:bg-gray-700">
Add Employee
</a>
</li>

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Accounts
</a>
</li>

</ul>
</li>

<!-- ACCOUNTS -->

<li x-data="{open:false}"
@mouseenter="open=true"
@mouseleave="open=false"
class="relative nav-item">

<button>
Accounts
</button>

<ul x-show="open"
x-transition
@mouseenter="open=true"
@mouseleave="open=false"
class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Account Sheet
</a>
</li>

</ul>
</li>

<!-- BILL BOOK -->

<li x-data="{open:false}"
@mouseenter="open=true"
@mouseleave="open=false"
class="relative nav-item">

<button>
Bill Book
</button>

<ul x-show="open"
x-transition
@mouseenter="open=true"
@mouseleave="open=false"
class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">

<li>
<a href="{{ route('billing.create') }}" class="block px-4 py-2 hover:bg-gray-700">
Billing Form
</a>
</li>

<li>
<a href="{{ route('billing.display') }}" class="block px-4 py-2 hover:bg-gray-700">
 Bill Book
</a>
</li>

</ul>
</li>

<!-- TRIPS -->

<li x-data="{open:false}"
@mouseenter="open=true"
@mouseleave="open=false"
class="relative nav-item">

<button>
Trips
</button>

<ul x-show="open"
x-transition
@mouseenter="open=true"
@mouseleave="open=false"
class="absolute left-0 mt-2 w-56 bg-gray-800 rounded shadow-xl py-2">

<li>
<a href="{{ route('company.add') }}" class="block px-4 py-2 hover:bg-gray-700">
Add Company
</a>
</li>

<li>
<a href="{{ route('destination.index') }}" class="block px-4 py-2 hover:bg-gray-700">
Add Destination
</a>
</li>

<li>
<a href="{{ route('truck.index') }}" class="block px-4 py-2 hover:bg-gray-700">
Add Truck
</a>
</li>

<li>
<a href="{{ route('trip.index') }}" class="block px-4 py-2 hover:bg-gray-700">
Trip Sheet
</a>
</li>

<li>
<a href="{{ route('expense.index') }}" class="block px-4 py-2 hover:bg-gray-700">
Expense Sheet
</a>
</li>

</ul>
</li>

<!-- SPAREPARTS -->

<li x-data="{open:false}"
@mouseenter="open=true"
@mouseleave="open=false"
class="relative nav-item">

<button>
Spareparts
</button>

<ul x-show="open"
x-transition
@mouseenter="open=true"
@mouseleave="open=false"
class="absolute right-0 mt-2 w-56 bg-gray-800 rounded shadow-xl py-2">

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Add Supplier
</a>
</li>

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Add Sparepart
</a>
</li>

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Sale
</a>
</li>

<li>
<a href="#" class="block px-4 py-2 hover:bg-gray-700">
Stock Report
</a>
</li>

</ul>
</li>

</ul>

<!-- MOBILE BUTTON -->

<button class="md:hidden ml-auto text-3xl" onclick="toggleMobileMenu()">
☰
</button>

</div>

</nav>

<!-- MOBILE MENU -->

<div id="mobileMenu" class="hidden bg-gray-800 text-white p-4 md:hidden space-y-4">

<details class="submenu">

<summary class="py-2">Trips</summary>

<ul class="ml-6 space-y-1">

<li>
<a href="{{ route('company.add') }}" class="block py-1">
Add Company
</a>
</li>

<li>
<a href="{{ route('destination.index') }}" class="block py-1">
Add Destination
</a>
</li>

<li>
<a href="{{ route('trip.index') }}" class="block py-1">
Trip Sheet
</a>
</li>

<li>
<a href="{{ route('expense.index') }}" class="block py-1">
Expense Sheet
</a>
</li>

</ul>

</details>

</div>

<!-- MAIN CONTENT -->

<div class="w-full p-6">

@yield('content')

</div>

<script>

/* MOBILE MENU */

function toggleMobileMenu(){

const m=document.getElementById("mobileMenu")

m.classList.toggle("hidden")

setTimeout(()=>m.classList.toggle("open"),10)

}

/* CLOSE OTHER SUBMENUS */

document.querySelectorAll("#mobileMenu .submenu").forEach(el=>{

el.addEventListener("click",()=>{

document.querySelectorAll("#mobileMenu .submenu").forEach(other=>{

if(other!==el) other.removeAttribute("open")

})

})

})

</script>

</body>
</html>
