
<?php
include('allhead.php');
include_once("../connection.php");


/* =========================================================
   DELETE STUDENT
========================================================= */

if (isset($_GET['delete'])) {

    $s_id = mysqli_real_escape_string(
        $link,
        $_GET['delete']
    );


    /*
     * Delete related student records first
     */

    mysqli_query(
        $link,
        "DELETE FROM student_classes WHERE s_id='$s_id'"
    );

    mysqli_query(
        $link,
        "DELETE FROM attendance WHERE s_id='$s_id'"
    );

    mysqli_query(
        $link,
        "DELETE FROM payment WHERE s_id='$s_id'"
    );


    /*
     * Delete student
     */

    $deleteStudent = mysqli_query(
        $link,
        "DELETE FROM student WHERE s_id='$s_id'"
    );


    if ($deleteStudent) {

        echo "<script>
                alert('Student deleted successfully.');
                window.location='managestudent.php';
              </script>";

        exit;

    } else {

        echo "<script>
                alert('Error deleting student.');
              </script>";

    }
}


/* =========================================================
   UPDATE STUDENT
========================================================= */

if (isset($_POST['update_student'])) {

    $s_id = mysqli_real_escape_string(
        $link,
        $_POST['s_id']
    );

    $SName = mysqli_real_escape_string(
        $link,
        $_POST['SName']
    );

    $Pname = mysqli_real_escape_string(
        $link,
        $_POST['Pname']
    );

    $School = mysqli_real_escape_string(
        $link,
        $_POST['School']
    );

    $Address = mysqli_real_escape_string(
        $link,
        $_POST['Address']
    );

    $WNumber = mysqli_real_escape_string(
        $link,
        $_POST['WNumber']
    );

    $VNumber = mysqli_real_escape_string(
        $link,
        $_POST['VNumber']
    );

    $NNumber = mysqli_real_escape_string(
        $link,
        $_POST['NNumber']
    );

    $Register_date = mysqli_real_escape_string(
        $link,
        $_POST['Register_date']
    );


    /*
     * Update student details
     */

    $update = mysqli_query(
        $link,

        "UPDATE student SET

            SName='$SName',
            Pname='$Pname',
            School='$School',
            Address='$Address',
            WNumber='$WNumber',
            VNumber='$VNumber',
            NNumber='$NNumber',
            Register_date='$Register_date'

         WHERE s_id='$s_id'"
    );


    if ($update) {

        echo "<script>
                alert('Student details updated successfully.');
                window.location='managestudent.php';
              </script>";

        exit;

    } else {

        echo "<script>
                alert('Error updating student: " .
                mysqli_error($link) .
                "');
              </script>";

    }
}


/* =========================================================
   GET SELECTED FILTERS
========================================================= */

$selectedGrade = isset($_GET['grade'])
    ? trim($_GET['grade'])
    : '';

$selectedSubject = isset($_GET['subject'])
    ? trim($_GET['subject'])
    : '';


/* =========================================================
   GET ALL GRADES
========================================================= */

$gradeQuery = "
    SELECT DISTINCT grade
    FROM classess
    WHERE grade IS NOT NULL
    AND grade != ''
    ORDER BY grade ASC
";

$gradeResult = mysqli_query(
    $link,
    $gradeQuery
);


/* =========================================================
   GET ALL SUBJECTS
========================================================= */

$subjectQuery = "
    SELECT DISTINCT subject
    FROM classess
    WHERE subject IS NOT NULL
    AND subject != ''
    ORDER BY subject ASC
";

$subjectResult = mysqli_query(
    $link,
    $subjectQuery
);


/* =========================================================
   GET STUDENTS
========================================================= */

/*
 * We use student_classes to find which class each
 * student belongs to.
 *
 * Grade and Subject come from classess table.
 */

$query = "
    SELECT DISTINCT

        s.s_id,
        s.SName,
        s.Pname,
        s.School,
        s.Address,
        s.WNumber,
        s.VNumber,
        s.NNumber,
        s.Register_date

    FROM student s

    LEFT JOIN student_classes sc
        ON s.s_id = sc.s_id

    LEFT JOIN classess c
        ON sc.class_id = c.class_id

    WHERE 1=1
";


/* =========================================================
   GRADE FILTER
========================================================= */

if ($selectedGrade != '') {

    $safeGrade = mysqli_real_escape_string(
        $link,
        $selectedGrade
    );

    $query .= "
        AND c.grade='$safeGrade'
    ";
}


/* =========================================================
   SUBJECT FILTER
========================================================= */

if ($selectedSubject != '') {

    $safeSubject = mysqli_real_escape_string(
        $link,
        $selectedSubject
    );

    $query .= "
        AND c.subject='$safeSubject'
    ";
}


/*
 * We do NOT use ORDER BY here.
 *
 * Student records will be sorted using our
 * Quick Sort algorithm below.
 */

$result = mysqli_query(
    $link,
    $query
);


/* =========================================================
   STORE STUDENTS IN ARRAY
========================================================= */

$students = array();


if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        $students[] = $row;

    }

}


/* =========================================================
   QUICK SORT ALGORITHM
========================================================= */

/*
 * Quick Sort is a fast sorting algorithm.
 *
 * Average Time Complexity : O(n log n)
 * Worst Time Complexity   : O(n²)
 * Space Complexity        : O(log n) average recursion
 *
 * Students are sorted according to Student ID.
 */


/* =========================================================
   QUICK SORT FUNCTION
========================================================= */

function quickSortStudents(&$students, $low, $high)
{

    if ($low < $high) {

        /*
         * Find partition position
         */

        $pivotIndex = partitionStudents(
            $students,
            $low,
            $high
        );


        /*
         * Sort left side
         */

        quickSortStudents(
            $students,
            $low,
            $pivotIndex - 1
        );


        /*
         * Sort right side
         */

        quickSortStudents(
            $students,
            $pivotIndex + 1,
            $high
        );

    }

}


/* =========================================================
   PARTITION FUNCTION
========================================================= */

function partitionStudents(&$students, $low, $high)
{

    /*
     * Select last student ID as pivot
     */

    $pivot = $students[$high]['s_id'];


    /*
     * Index of smaller element
     */

    $i = $low - 1;


    for ($j = $low; $j < $high; $j++) {

        /*
         * Compare Student IDs
         */

        if (
            strcasecmp(
                $students[$j]['s_id'],
                $pivot
            ) <= 0
        ) {

            $i++;


            /*
             * Swap students
             */

            $temp = $students[$i];

            $students[$i] = $students[$j];

            $students[$j] = $temp;

        }

    }


    /*
     * Put pivot in correct position
     */

    $temp = $students[$i + 1];

    $students[$i + 1] = $students[$high];

    $students[$high] = $temp;


    return $i + 1;

}


/* =========================================================
   APPLY QUICK SORT
========================================================= */

if (count($students) > 1) {

    quickSortStudents(
        $students,
        0,
        count($students) - 1
    );

}

?>


<style>

/* =========================================================
   PAGE
========================================================= */

body{

    background:#eef2f7;

}


/* =========================================================
   CONTAINER
========================================================= */

.student-container{

    width:100%;

    background:white;

    padding:20px;

    border-radius:14px;

    box-shadow:0 6px 18px rgba(0,0,0,0.08);

    margin-top:20px;

}


/* =========================================================
   TITLE
========================================================= */

.page-title{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:20px;

}


.page-title h3{

    margin:0;

    font-weight:600;

}


/* =========================================================
   FILTER CONTAINER
========================================================= */

.filter-container{

    background:#f8fafc;

    border:1px solid #e5e7eb;

    border-radius:10px;

    padding:15px;

    margin-bottom:20px;

}


/* =========================================================
   FILTER TITLE
========================================================= */

.filter-title{

    font-size:15px;

    font-weight:600;

    margin-bottom:12px;

}


/* =========================================================
   FILTER FORM
========================================================= */

.filter-form{

    display:flex;

    align-items:end;

    gap:12px;

    flex-wrap:wrap;

}


/* =========================================================
   FILTER GROUP
========================================================= */

.filter-group{

    display:flex;

    flex-direction:column;

    gap:5px;

}


.filter-group label{

    font-size:13px;

    font-weight:500;

    color:#374151;

}


.filter-group select{

    min-width:200px;

    padding:8px 10px;

    border:1px solid #ced4da;

    border-radius:5px;

    background:white;

    font-size:13px;

}


.filter-group select:focus{

    outline:none;

    border-color:#4e73df;

    box-shadow:0 0 0 2px rgba(78,115,223,0.15);

}


/* =========================================================
   FILTER BUTTON
========================================================= */

.btn-filter{

    background:#4e73df;

    color:white;

    border:none;

    padding:8px 15px;

    border-radius:5px;

    cursor:pointer;

    font-size:13px;

}


.btn-filter:hover{

    background:#224abe;

}


/* =========================================================
   CLEAR BUTTON
========================================================= */

.btn-clear{

    background:#6c757d;

    color:white;

    padding:8px 15px;

    border-radius:5px;

    text-decoration:none;

    font-size:13px;

    display:inline-block;

}


.btn-clear:hover{

    background:#545b62;

    color:white;

}


/* =========================================================
   FILTER INFORMATION
========================================================= */

.filter-info{

    margin-top:12px;

    padding:8px 10px;

    background:#eef2ff;

    border-radius:5px;

    color:#374151;

    font-size:13px;

}


.filter-info strong{

    color:#111827;

}


/* =========================================================
   TABLE CONTAINER
========================================================= */

.table-container{

    width:100%;

    overflow-x:auto;

}


/* =========================================================
   TABLE
========================================================= */

.student-table{

    width:100%;

    min-width:1100px;

    border-collapse:collapse;

}


.student-table th{

    background:#111827;

    color:white;

    padding:12px;

    text-align:left;

    font-size:13px;

    white-space:nowrap;

}


.student-table td{

    padding:10px;

    border-bottom:1px solid #e5e7eb;

    font-size:13px;

    vertical-align:middle;

}


.student-table tr:hover{

    background:#f8fafc;

}


/* =========================================================
   INPUTS
========================================================= */

.student-table input{

    width:100%;

    min-width:100px;

    padding:7px 8px;

    border:1px solid #ced4da;

    border-radius:5px;

    font-size:13px;

}


.student-table input:focus{

    outline:none;

    border-color:#4e73df;

    box-shadow:0 0 0 2px rgba(78,115,223,0.15);

}


/* =========================================================
   STUDENT ID
========================================================= */

.student-table input[name="s_id"]{

    background:#f1f3f5;

    cursor:not-allowed;

}


/* =========================================================
   ACTION COLUMN
========================================================= */

.action-column{

    min-width:160px;

    text-align:center;

    white-space:nowrap;

}


/* =========================================================
   BUTTONS
========================================================= */

.btn-edit{

    background:#4e73df;

    color:white;

    border:none;

    padding:7px 12px;

    border-radius:5px;

    cursor:pointer;

    font-size:13px;

}


.btn-edit:hover{

    background:#224abe;

}


.btn-delete{

    background:#e74a3b;

    color:white;

    border:none;

    padding:7px 12px;

    border-radius:5px;

    cursor:pointer;

    font-size:13px;

}


.btn-delete:hover{

    background:#c0392b;

}


.btn-update{

    background:#1cc88a;

    color:white;

    border:none;

    padding:7px 12px;

    border-radius:5px;

    cursor:pointer;

    font-size:13px;

}


.btn-update:hover{

    background:#0f9d58;

}


.btn-cancel{

    background:#6c757d;

    color:white;

    border:none;

    padding:7px 12px;

    border-radius:5px;

    cursor:pointer;

    font-size:13px;

}


.btn-cancel:hover{

    background:#545b62;

}


/* =========================================================
   NO DATA
========================================================= */

.no-data{

    text-align:center;

    padding:30px;

    color:#6b7280;

}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:768px){

    .student-container{

        padding:12px;

        margin-top:15px;

    }


    .page-title{

        align-items:flex-start;

        flex-direction:column;

        gap:10px;

    }


    .filter-form{

        flex-direction:column;

        align-items:stretch;

    }


    .filter-group{

        width:100%;

    }


    .filter-group select{

        width:100%;

    }


    .btn-filter,
    .btn-clear{

        width:100%;

        text-align:center;

    }


    .student-table{

        min-width:1100px;

    }

}

</style>


<div class="student-container">


    <!-- =====================================================
         TITLE
    ====================================================== -->

    <div class="page-title">

        <h3>

            <i class="fa fa-user-graduate"></i>

            Student Details

        </h3>


        <a
            href="welcomeadmin.php"
            class="btn btn-warning">

            <i class="fa fa-arrow-left"></i>

            Back

        </a>

    </div>


    <!-- =====================================================
         FILTER SECTION
    ====================================================== -->

    <div class="filter-container">


        <div class="filter-title">

            <i class="fa fa-filter"></i>

            Filter Students

        </div>


        <form
            method="GET"
            action="managestudent.php"
            class="filter-form">


            <!-- =================================================
                 GRADE
            ================================================== -->

            <div class="filter-group">

                <label for="grade">

                    Grade

                </label>


                <select
                    name="grade"
                    id="grade">

                    <option value="">

                        All Grades

                    </option>


                    <?php

                    if (
                        $gradeResult &&
                        mysqli_num_rows($gradeResult) > 0
                    ) {

                        while (
                            $gradeRow =
                            mysqli_fetch_assoc($gradeResult)
                        ) {

                    ?>

                        <option
                            value="<?php echo htmlspecialchars($gradeRow['grade']); ?>"
                            <?php

                            if (
                                $selectedGrade ==
                                $gradeRow['grade']
                            ) {

                                echo 'selected';

                            }

                            ?>>

                            <?php
                            echo htmlspecialchars(
                                $gradeRow['grade']
                            );
                            ?>

                        </option>


                    <?php

                        }

                    }

                    ?>

                </select>

            </div>


            <!-- =================================================
                 SUBJECT
            ================================================== -->

            <div class="filter-group">

                <label for="subject">

                    Subject

                </label>


                <select
                    name="subject"
                    id="subject">

                    <option value="">

                        All Subjects

                    </option>


                    <?php

                    if (
                        $subjectResult &&
                        mysqli_num_rows($subjectResult) > 0
                    ) {

                        while (
                            $subjectRow =
                            mysqli_fetch_assoc($subjectResult)
                        ) {

                    ?>

                        <option
                            value="<?php echo htmlspecialchars($subjectRow['subject']); ?>"
                            <?php

                            if (
                                $selectedSubject ==
                                $subjectRow['subject']
                            ) {

                                echo 'selected';

                            }

                            ?>>

                            <?php
                            echo htmlspecialchars(
                                $subjectRow['subject']
                            );
                            ?>

                        </option>


                    <?php

                        }

                    }

                    ?>

                </select>

            </div>


            <!-- =================================================
                 FILTER BUTTON
            ================================================== -->

            <button
                type="submit"
                class="btn-filter">

                <i class="fa fa-filter"></i>

                Apply Filter

            </button>


            <!-- =================================================
                 CLEAR BUTTON
            ================================================== -->

            <a
                href="managestudent.php"
                class="btn-clear">

                <i class="fa fa-refresh"></i>

                Clear

            </a>


        </form>


        <!-- =====================================================
             FILTER INFORMATION
        ====================================================== -->

        <div class="filter-info">

            <?php

            if (
                $selectedGrade != '' &&
                $selectedSubject != ''
            ) {

                ?>

                Showing students for

                <strong>
                    Grade <?php echo htmlspecialchars($selectedGrade); ?>
                </strong>

                and

                <strong>
                    <?php echo htmlspecialchars($selectedSubject); ?>
                </strong>

                <?php

            } elseif ($selectedGrade != '') {

                ?>

                Showing students for

                <strong>
                    Grade <?php echo htmlspecialchars($selectedGrade); ?>
                </strong>

                <?php

            } elseif ($selectedSubject != '') {

                ?>

                Showing students for

                <strong>
                    <?php echo htmlspecialchars($selectedSubject); ?>
                </strong>

                <?php

            } else {

                ?>

                Showing

                <strong>
                    All Students
                </strong>

                <?php

            }

            ?>

            |

            Total Students:

            <strong>
                <?php echo count($students); ?>
            </strong>

            |

            Sorted by:

            <strong>
                Student ID
            </strong>

            using Quick Sort

        </div>


    </div>


    <!-- =====================================================
         STUDENT TABLE
    ====================================================== -->

    <div class="table-container">

        <table class="student-table">


            <thead>

                <tr>

                    <th>S ID</th>

                    <th>Student Name</th>

                    <th>Parent Name</th>

                    <th>School</th>

                    <th>Address</th>

                    <th>WhatsApp Number</th>

                    <th>Voice Number</th>

                    <th>NIC Number</th>

                    <th>Register Date</th>

                    <th class="action-column">

                        Action

                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            if (count($students) > 0) {

                foreach ($students as $row) {

            ?>


                <!-- =================================================
                     STUDENT ROW
                ================================================== -->

                <tr>


                    <!-- S ID -->

                    <td>

                        <input
                            type="text"
                            name="s_id"
                            value="<?php
                            echo htmlspecialchars(
                                $row['s_id']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- STUDENT NAME -->

                    <td>

                        <input
                            type="text"
                            name="SName"
                            value="<?php
                            echo htmlspecialchars(
                                $row['SName']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- PARENT NAME -->

                    <td>

                        <input
                            type="text"
                            name="Pname"
                            value="<?php
                            echo htmlspecialchars(
                                $row['Pname']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- SCHOOL -->

                    <td>

                        <input
                            type="text"
                            name="School"
                            value="<?php
                            echo htmlspecialchars(
                                $row['School']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- ADDRESS -->

                    <td>

                        <input
                            type="text"
                            name="Address"
                            value="<?php
                            echo htmlspecialchars(
                                $row['Address']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- WHATSAPP -->

                    <td>

                        <input
                            type="text"
                            name="WNumber"
                            value="<?php
                            echo htmlspecialchars(
                                $row['WNumber']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- VOICE NUMBER -->

                    <td>

                        <input
                            type="text"
                            name="VNumber"
                            value="<?php
                            echo htmlspecialchars(
                                $row['VNumber']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- NIC NUMBER -->

                    <td>

                        <input
                            type="text"
                            name="NNumber"
                            value="<?php
                            echo htmlspecialchars(
                                $row['NNumber']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- REGISTER DATE -->

                    <td>

                        <input
                            type="date"
                            name="Register_date"
                            value="<?php
                            echo htmlspecialchars(
                                $row['Register_date']
                            );
                            ?>"
                            readonly>

                    </td>


                    <!-- ACTION -->

                    <td class="action-column">


                        <!-- EDIT -->

                        <button
                            type="button"
                            class="btn-edit"
                            onclick="editStudent(this)">

                            <i class="fa fa-pen"></i>

                            Edit

                        </button>


                        <!-- DELETE -->

                        <button
                            type="button"
                            class="btn-delete"
                            onclick="deleteStudent('<?php
                                echo htmlspecialchars(
                                    $row['s_id'],
                                    ENT_QUOTES
                                );
                            ?>')">

                            <i class="fa fa-trash"></i>

                            Delete

                        </button>


                    </td>


                </tr>


            <?php

                }

            } else {

            ?>


                <tr>

                    <td
                        colspan="10"
                        class="no-data">

                        <i class="fa fa-user-slash"></i>

                        No student records found.

                    </td>

                </tr>


            <?php

            }

            ?>


            </tbody>


        </table>

    </div>


</div>


<script>

/* =========================================================
   EDIT STUDENT
========================================================= */

function editStudent(button){

    /*
     * Get current row
     */

    const row =
        button.closest('tr');


    /*
     * Get all inputs
     */

    const inputs =
        row.querySelectorAll('input');


    /*
     * Enable all inputs except Student ID
     */

    inputs.forEach(function(input){

        if(input.name !== 's_id'){

            input.removeAttribute('readonly');

        }

    });


    /*
     * Change action buttons
     */

    const actionCell =
        row.querySelector('.action-column');


    actionCell.innerHTML = `

        <button
            type="button"
            class="btn-update"
            onclick="updateStudent(this)">

            <i class="fa fa-save"></i>

            Update

        </button>


        <button
            type="button"
            class="btn-cancel"
            onclick="cancelEdit(this)">

            <i class="fa fa-times"></i>

            Cancel

        </button>

    `;

}


/* =========================================================
   UPDATE STUDENT
========================================================= */

function updateStudent(button){

    /*
     * Get current row
     */

    const row =
        button.closest('tr');


    /*
     * Get values
     */

    const s_id =
        row.querySelector(
            '[name="s_id"]'
        ).value;


    const SName =
        row.querySelector(
            '[name="SName"]'
        ).value;


    const Pname =
        row.querySelector(
            '[name="Pname"]'
        ).value;


    const School =
        row.querySelector(
            '[name="School"]'
        ).value;


    const Address =
        row.querySelector(
            '[name="Address"]'
        ).value;


    const WNumber =
        row.querySelector(
            '[name="WNumber"]'
        ).value;


    const VNumber =
        row.querySelector(
            '[name="VNumber"]'
        ).value;


    const NNumber =
        row.querySelector(
            '[name="NNumber"]'
        ).value;


    const Register_date =
        row.querySelector(
            '[name="Register_date"]'
        ).value;


    /*
     * Validate student name
     */

    if(SName.trim() === ''){

        alert(
            'Please enter student name.'
        );

        return;

    }


    /*
     * Create POST form
     */

    const form =
        document.createElement('form');


    form.method = 'POST';

    form.action =
        'managestudent.php';


    /*
     * Add POST values
     */

    addHiddenInput(
        form,
        'update_student',
        '1'
    );


    addHiddenInput(
        form,
        's_id',
        s_id
    );


    addHiddenInput(
        form,
        'SName',
        SName
    );


    addHiddenInput(
        form,
        'Pname',
        Pname
    );


    addHiddenInput(
        form,
        'School',
        School
    );


    addHiddenInput(
        form,
        'Address',
        Address
    );


    addHiddenInput(
        form,
        'WNumber',
        WNumber
    );


    addHiddenInput(
        form,
        'VNumber',
        VNumber
    );


    addHiddenInput(
        form,
        'NNumber',
        NNumber
    );


    addHiddenInput(
        form,
        'Register_date',
        Register_date
    );


    /*
     * Add form to body
     */

    document.body.appendChild(form);


    /*
     * Submit form
     */

    form.submit();

}


/* =========================================================
   CREATE HIDDEN INPUT
========================================================= */

function addHiddenInput(
    form,
    name,
    value
){

    const input =
        document.createElement('input');


    input.type = 'hidden';

    input.name = name;

    input.value = value;


    form.appendChild(input);

}


/* =========================================================
   CANCEL EDIT
========================================================= */

function cancelEdit(button){

    /*
     * Reload page
     */

    window.location.href =
        'managestudent.php';

}


/* =========================================================
   DELETE STUDENT
========================================================= */

function deleteStudent(s_id){

    /*
     * Confirmation
     */

    const confirmDelete = confirm(

        'Are you sure you want to delete this student?\n\n' +

        'Student ID: ' + s_id + '\n\n' +

        'This will also delete the student\'s related ' +
        'class, attendance and payment records.\n\n' +

        'This action cannot be undone.'

    );


    /*
     * Cancel delete
     */

    if(!confirmDelete){

        return;

    }


    /*
     * Delete student
     */

    window.location.href =
        'managestudent.php?delete=' +
        encodeURIComponent(s_id);

}

</script>


<?php include('allfoot.php'); ?>
