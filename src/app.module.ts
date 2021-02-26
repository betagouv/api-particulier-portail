import { Module } from '@nestjs/common';
import { TerminusModule } from '@nestjs/terminus';
import { AuthenticationModule } from 'src/authentication/authentication.module';
import { GraviteeModule } from 'src/gravitee/gravitee.module';
import { AppController } from './app.controller';
import { HealthController } from './health/health.controller';

@Module({
  imports: [AuthenticationModule, GraviteeModule, TerminusModule],
  controllers: [AppController, HealthController],
})
export class AppModule {}
