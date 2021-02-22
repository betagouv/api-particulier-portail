import { Module } from '@nestjs/common';
import { AuthenticationModule } from 'src/authentication/authentication.module';
import { GraviteeModule } from 'src/gravitee/gravitee.module';
import { AppController } from './app.controller';

@Module({
  imports: [AuthenticationModule, GraviteeModule],
  controllers: [AppController],
})
export class AppModule {}
