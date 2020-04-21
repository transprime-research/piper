# php-pipe
Pipe method execution with values from initial call like F# let finalSeq = seq { 0..10 } |> Seq.filter (fun c -> (c % 2) = 0) |> Seq.map ((*) 2) |> Seq.map (sprintf "The value is %i.")

##Usage

```php
pipe(2)->to(fn($value) => 'array_map')->to(fn($value) => implode($value));
```