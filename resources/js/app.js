import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css'; 
import Swal from 'sweetalert2';
import DataTable from 'datatables.net-dt';
 
let table = new DataTable('#myTable');
window.Swal = Swal;
