const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});




// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})







const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if(window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if(searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
})





if(window.innerWidth < 768) {
	sidebar.classList.add('hide');
} else if(window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}


window.addEventListener('resize', function () {
	if(this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
})



const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if(this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}
})



// Add table

// Get the table element
const table = document.getElementById("PatientTable");

// Get the button element
const addButton = document.getElementById("add_patient");

//Number track
var no_of_patient = 0;

// Add event listener to the button
addButton.addEventListener("click", function() {
    no_of_patient++;
    // Create a new row
    const newRow = document.createElement("tr");

    // Create the cells for the new row
    const cell1 = document.createElement("td");
    const cell2 = document.createElement("td");
    const cell3 = document.createElement("td");
    const cell4 = document.createElement("td");
    const cell5 = document.createElement("td");
    const cell6 = document.createElement("td");

    // Set the content of the cells
    cell1.textContent = "PT_240"+no_of_patient;
    cell2.innerHTML = "<input type=\"text\" id=\"patient_name\" name=\"patient_name\" placeholder=\"Name...\">";
    cell3.innerHTML = "<input type=\"number\" id=\"Age\" name=\"Age\">";
    cell4.innerHTML = "<input type=\"number\" id=\"Phone\" name=\"Phone\">";
    cell5.innerHTML = "<select name=\"Blood_Group\" id=\"BG\"><option value=\"A+\">A+</option><option value=\"A-\">A-</option><option value=\"B+\">B+</option><option value=\"B-\">B-</option><option value=\"AB-\">AB-</option><option value=\"AB+\">AB+</option><option value=\"O+\">O+</option><option value=\"O-\">O-</option></select>";
    cell6.innerHTML = "<a href=\MakePrescription.html\>Make Prescription</a>";
    


    // Append the cells to the new row
    newRow.appendChild(cell1);
    newRow.appendChild(cell2);
    newRow.appendChild(cell3);
    newRow.appendChild(cell4);
    newRow.appendChild(cell5);
    newRow.appendChild(cell6);

    // Append the new row to the table
    table.appendChild(newRow);
});