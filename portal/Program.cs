using System;
using Microsoft.AspNetCore.Builder;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using WebHackingPortal.Data;
using WebHackingPortal.Services;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
builder.Services.AddSingleton<LabService>();

builder.Services.AddRazorPages();
builder.Services.AddHttpContextAccessor();

// Register DB Context
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseSqlite(builder.Configuration.GetConnectionString("DefaultConnection")));

// Register Auth & Progress Services
builder.Services.AddScoped<AuthService>();
builder.Services.AddSession(); // At the top with other services
builder.Services.AddAuthentication("Cookie").AddCookie(options =>
{
    options.Cookie.Name = "WebHackingPortal.Auth";
    options.ExpireTimeSpan = TimeSpan.FromDays(7);
    options.SlidingExpiration = true;
});
builder.Services.AddHttpContextAccessor(); // Needed if AuthService uses IHttpContextAccessor
var app = builder.Build();

// ✅ Run migrations on startup
using var scope = app.Services.CreateScope();
var dbContext = scope.ServiceProvider.GetRequiredService<AppDbContext>();
await dbContext.Database.MigrateAsync(); // This creates the DB and applies migrations

// Middleware
app.UseHttpsRedirection();
app.UseStaticFiles();

app.UseRouting();
app.UseSession(); // After UseRouting(), but before UseAuthorization()
app.UseAuthentication();

app.UseAuthorization();
app.MapRazorPages();

app.Run();