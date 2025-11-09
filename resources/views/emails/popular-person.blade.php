<!DOCTYPE html>
<html>
<head>
    <title>Popular Person Alert</title>
</head>
<body>
    <h1>Popular Person Alert!</h1>
    <p><strong>{{ $person->name }}</strong> has received more than 50 likes!</p>
    <p><strong>Total Likes:</strong> {{ $person->like_count }}</p>
    <p><strong>Age:</strong> {{ $person->age }}</p>
    <p><strong>Location:</strong> {{ $person->location }}</p>
</body>
</html>

