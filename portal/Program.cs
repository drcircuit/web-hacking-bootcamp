using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using WebHackingPortal.Data;
using WebHackingPortal.Models;
using WebHackingPortal.Services;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
builder.Services.AddRazorPages();
builder.Services.AddHttpContextAccessor();

// Register DB Context
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseSqlite(builder.Configuration.GetConnectionString("DefaultConnection")));

// Register Auth & Progress Services
builder.Services.AddScoped<AuthService>();
builder.Services.AddScoped<ProgressService>();

var app = builder.Build();

// Configure and seed data
if (app.Environment.IsDevelopment())
{
    using var scope = app.Services.CreateScope();
    var dbContext = scope.ServiceProvider.GetRequiredService<AppDbContext>();
    await dbContext.Database.MigrateAsync();

    // Seed default user
    if (!dbContext.Users.Any(u => u.Username == "student"))
    {
        dbContext.Users.Add(new AppUser
        {
            Username = "student",
            PasswordHash = BCrypt.Net.BCrypt.HashPassword("password")
        });
        await dbContext.SaveChangesAsync();
    }

    // Load challenges from JSON
    var labDir = Path.Combine(Directory.GetCurrentDirectory(), "Labs");
    if (Directory.Exists(labDir))
    {
        foreach (var file in Directory.GetFiles(labDir, "*.json"))
        {
            var json = await File.ReadAllTextAsync(file);
            var lab = System.Text.Json.JsonSerializer.Deserialize<Lab>(json);

            if (lab != null)
            {
                if (!dbContext.Labs.Any(l => l.Id == lab.Id))
                {
                    dbContext.Labs.Add(lab);
                }
                else
                {
                    var existingLab = await dbContext.Labs
                        .Include(l => l.Challenges)
                        .FirstOrDefaultAsync(l => l.Id == lab.Id);

                    if (existingLab != null)
                    {
                        // Replace old challenges with updated ones
                        dbContext.Challenges.RemoveRange(existingLab.Challenges);
                        existingLab.Challenges = lab.Challenges;
                        dbContext.Labs.Update(existingLab);
                    }
                }
                await dbContext.SaveChangesAsync();
            }
        }
    }
}

// Middleware
app.UseHttpsRedirection();
app.UseStaticFiles();
app.UseRouting();
app.UseAuthentication();
app.UseAuthorization();
app.MapRazorPages();

app.Run();