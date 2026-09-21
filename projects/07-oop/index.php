<?php
// ─── Interface ────────────────────────────────────────────────────────────────
interface Describable {
    public function describe(): string;
}

// ─── Abstract Base ────────────────────────────────────────────────────────────
abstract class Shape implements Describable {
    abstract public function area(): float;
    abstract public function perimeter(): float;

    public function describe(): string {
        return sprintf(
            "%s — Area: %.2f, Perimeter: %.2f",
            get_class($this),
            $this->area(),
            $this->perimeter()
        );
    }
}

// ─── Concrete Classes ─────────────────────────────────────────────────────────
class Circle extends Shape {
    public function __construct(private float $radius) {}

    public function area(): float {
        return M_PI * $this->radius ** 2;
    }

    public function perimeter(): float {
        return 2 * M_PI * $this->radius;
    }
}

class Rectangle extends Shape {
    public function __construct(
        private float $width,
        private float $height
    ) {}

    public function area(): float {
        return $this->width * $this->height;
    }

    public function perimeter(): float {
        return 2 * ($this->width + $this->height);
    }
}

class Triangle extends Shape {
    public function __construct(
        private float $a,
        private float $b,
        private float $c
    ) {}

    public function area(): float {
        $s = ($this->a + $this->b + $this->c) / 2;
        return sqrt($s * ($s - $this->a) * ($s - $this->b) * ($s - $this->c));
    }

    public function perimeter(): float {
        return $this->a + $this->b + $this->c;
    }
}

// ─── Trait ────────────────────────────────────────────────────────────────────
trait Colorable {
    private string $color = "white";

    public function setColor(string $color): static {
        $this->color = $color;
        return $this;
    }

    public function getColor(): string {
        return $this->color;
    }
}

class ColoredCircle extends Circle {
    use Colorable;
}

// ─── Enum (PHP 8.1+) ──────────────────────────────────────────────────────────
enum ShapeType: string {
    case Circle    = "circle";
    case Rectangle = "rectangle";
    case Triangle  = "triangle";
}

// ─── Demo ─────────────────────────────────────────────────────────────────────
$shapes = [
    new Circle(5),
    new Rectangle(4, 7),
    new Triangle(3, 4, 5),
];

echo "<h2>Shapes</h2>";
foreach ($shapes as $shape) {
    echo $shape->describe() . "<br>";
}

echo "<h2>Colored Circle (Trait)</h2>";
$cc = new ColoredCircle(3);
$cc->setColor("red");
echo $cc->describe() . " — Color: " . $cc->getColor() . "<br>";

echo "<h2>Enum</h2>";
foreach (ShapeType::cases() as $case) {
    echo $case->name . " => " . $case->value . "<br>";
}

// ─── Static ───────────────────────────────────────────────────────────────────
echo "<h2>Static Counter</h2>";
class ShapeFactory {
    private static int $created = 0;

    public static function make(string $type): Shape {
        self::$created++;
        return match($type) {
            "circle"    => new Circle(rand(1, 10)),
            "rectangle" => new Rectangle(rand(1, 8), rand(1, 8)),
            default     => new Triangle(3, 4, 5),
        };
    }

    public static function totalCreated(): int {
        return self::$created;
    }
}

ShapeFactory::make("circle");
ShapeFactory::make("rectangle");
ShapeFactory::make("triangle");
echo "Shapes created via factory: " . ShapeFactory::totalCreated() . "<br>";
