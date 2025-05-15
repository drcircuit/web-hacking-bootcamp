using Microsoft.EntityFrameworkCore;
using WebHackingPortal.Models;

namespace WebHackingPortal.Data;
public class AppDbContext : DbContext
{
    public DbSet<AppUser> Users { get; set; }
    public DbSet<Submission> Submissions { get; set; }

    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options)
    {
    }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        modelBuilder.Entity<AppUser>().ToTable("Users");
        modelBuilder.Entity<Submission>().ToTable("Submissions");

        modelBuilder.Entity<Submission>()
            .HasOne<AppUser>()
            .WithMany()
            .HasForeignKey(s => s.UserId)
            .OnDelete(DeleteBehavior.Cascade); // Optional
    }
}