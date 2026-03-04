<!DOCTYPE html>
<html>
<head>
    <title>Students Report</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Students Report</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Student ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->hu_name }}</td>
                    <td>{{ $student->hu_email }}</td>
                    <td>{{ $student->hu_phone }}</td>
                    <td>{{ $student->hu_student_id }}</td>
                    <td>{{ ucfirst($student->moderationStatusKey()) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
