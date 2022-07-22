<!DOCTYPE html>
<html>
<head>
 <title>Someone responded to your job</title>
</head>
<body>
 
    <h3>{{ $mailData['job_title'] }}</h3>
    <label>Total number of responses: <span>{{ count($mailData['users']) }}</span></label>
    <br/>
    
    <label>Responsed users: </label>
    <ul>
    @foreach($mailData['users'] as $user)
        <li>{{ $user['name'] }} - {{ $user['created_at'] }}</li>
    @endforeach
    </ul>
    
</body>
</html> 