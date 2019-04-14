<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <h1>Create Project</h1>
    
    <form method="POST" action="/projects">
        @csrf
        <div>
            <input type="text" name="title">
        </div>
        <div>
            <textarea name="description" cols="30" rows="10"></textarea>
        </div>
        <div>
            <button type="submit">Create</button>
        </div>
    </form>
</body>
</html>
